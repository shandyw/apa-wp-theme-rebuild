let youtubeApiPromise;
let vimeoApiPromise;

function loadScript(src) {
  return new Promise((resolve, reject) => {
    const existing = document.querySelector(`script[src="${src}"]`);

    if (existing) {
      existing.addEventListener('load', () => resolve());
      existing.addEventListener('error', reject);
      if (existing.dataset.loaded === 'true') {
        resolve();
      }
      return;
    }

    const script = document.createElement('script');
    script.src = src;
    script.async = true;
    script.addEventListener('load', () => {
      script.dataset.loaded = 'true';
      resolve();
    });
    script.addEventListener('error', reject);
    document.head.appendChild(script);
  });
}

function loadYouTubeApi() {
  if (window.YT?.Player) {
    return Promise.resolve(window.YT);
  }

  if (!youtubeApiPromise) {
    youtubeApiPromise = new Promise((resolve, reject) => {
      const previous = window.onYouTubeIframeAPIReady;

      window.onYouTubeIframeAPIReady = () => {
        if (typeof previous === 'function') {
          previous();
        }
        resolve(window.YT);
      };

      loadScript('https://www.youtube.com/iframe_api').catch(reject);
    });
  }

  return youtubeApiPromise;
}

function loadVimeoApi() {
  if (window.Vimeo?.Player) {
    return Promise.resolve(window.Vimeo);
  }

  if (!vimeoApiPromise) {
    vimeoApiPromise = loadScript('https://player.vimeo.com/api/player.js').then(() => window.Vimeo);
  }

  return vimeoApiPromise;
}

function formatTime(seconds) {
  const safeSeconds = Number.isFinite(seconds) && seconds > 0 ? Math.floor(seconds) : 0;
  const minutes = Math.floor(safeSeconds / 60);
  const remainder = safeSeconds % 60;
  return `${String(minutes).padStart(2, '0')}:${String(remainder).padStart(2, '0')}`;
}

function setPlayingState(root, isPlaying) {
  root.classList.toggle('is-playing', isPlaying);
}

function setStartedState(root, hasStarted) {
  root.classList.toggle('has-started', hasStarted);
}

function clearUiHideTimer(root) {
  if (typeof root.__apacheVideoHideUiTimer === 'number') {
    window.clearTimeout(root.__apacheVideoHideUiTimer);
    root.__apacheVideoHideUiTimer = 0;
  }
}

function setUiHiddenState(root, isHidden) {
  root.classList.toggle('is-ui-hidden', isHidden);
}

function setBufferingState(root, isBuffering) {
  root.classList.toggle('is-buffering', isBuffering);
}

function scheduleUiHide(root, delay = 0) {
  clearUiHideTimer(root);
  root.__apacheVideoHideUiTimer = window.setTimeout(() => {
    setUiHiddenState(root, true);
    root.__apacheVideoHideUiTimer = 0;
  }, delay);
}

function setStartingState(root, isStarting) {
  root.classList.toggle('is-starting', isStarting);
}

function setMutedState(root, isMuted) {
  root.classList.toggle('is-muted', Boolean(isMuted));
}

function updateTime(root, currentTime, duration) {
  const time = root.querySelector('[data-apache-video-time]');
  const progress = root.querySelector('[data-apache-video-progress]');
  const safeDuration = Number.isFinite(duration) && duration > 0 ? duration : 0;
  const safeCurrent = Number.isFinite(currentTime) && currentTime >= 0 ? currentTime : 0;
  const progressPercent = safeDuration > 0 ? (safeCurrent / safeDuration) * 100 : 0;

  if (time) {
    time.textContent = safeDuration > 0 ? `${formatTime(safeCurrent)} / ${formatTime(safeDuration)}` : formatTime(safeCurrent);
  }

  if (progress instanceof HTMLInputElement) {
    progress.value = String(progressPercent);
  }

  root.style.setProperty('--apache-video-progress', `${progressPercent}%`);
}

function createSelfHostedController(root, video) {
  const progress = root.querySelector('[data-apache-video-progress]');
  const volume = root.querySelector('[data-apache-video-volume]');

  const sync = () => {
    updateTime(root, video.currentTime, video.duration);
    const isPlaying = !video.paused && !video.ended;
    setPlayingState(root, isPlaying);
    if (isPlaying || video.currentTime > 0) {
      setStartedState(root, true);
    }
    setStartingState(root, false);
    clearUiHideTimer(root);
    if (isPlaying) {
      scheduleUiHide(root);
    } else {
      setUiHiddenState(root, false);
    }
    setMutedState(root, video.muted || video.volume === 0);
  };

  video.addEventListener('play', sync);
  video.addEventListener('pause', sync);
  video.addEventListener('ended', sync);
  video.addEventListener('timeupdate', sync);
  video.addEventListener('loadedmetadata', sync);
  video.addEventListener('volumechange', sync);

  if (progress instanceof HTMLInputElement) {
    progress.addEventListener('input', () => {
      if (video.duration) {
        video.currentTime = (Number(progress.value) / 100) * video.duration;
      }
    });
  }

  if (volume instanceof HTMLInputElement) {
    volume.addEventListener('input', () => {
      video.volume = Number(volume.value);
      video.muted = video.volume === 0;
    });
  }

  sync();

  return {
    play: () => video.play().catch(() => {}),
    pause: () => video.pause(),
    toggle: () => (video.paused ? video.play().catch(() => {}) : video.pause()),
    seekBy: (delta) => {
      video.currentTime = Math.max(0, Math.min((video.duration || 0), video.currentTime + delta));
      sync();
    },
    setMuted: (muted) => {
      video.muted = muted;
      if (!muted && video.volume === 0) {
        video.volume = 1;
      }
    },
    toggleMuted: () => {
      video.muted = !video.muted;
    },
    setVolume: (nextVolume) => {
      video.volume = nextVolume;
      video.muted = nextVolume === 0;
    },
    getVolume: () => (video.muted ? 0 : video.volume),
    isMuted: () => video.muted,
    isPlaying: () => !video.paused && !video.ended,
  };
}

async function createYouTubeController(root, iframe) {
  const YT = await loadYouTubeApi();
  const progress = root.querySelector('[data-apache-video-progress]');
  const volume = root.querySelector('[data-apache-video-volume]');
  let duration = 0;
  let rafId = 0;
  let player;

  const getMuted = () => (typeof player?.isMuted === 'function' ? player.isMuted() : false);
  const getPlayerVolume = () => (typeof player?.getVolume === 'function' ? player.getVolume() || 0 : 0);
  const setPlayerVolume = (nextVolume) => {
    if (typeof player?.setVolume === 'function') {
      player.setVolume(nextVolume);
    }
  };
  const mutePlayer = () => {
    if (typeof player?.mute === 'function') {
      player.mute();
    }
  };
  const unmutePlayer = () => {
    if (typeof player?.unMute === 'function') {
      player.unMute();
    }
  };

  player = new YT.Player(iframe, {
    events: {
      onReady: () => {
        duration = player.getDuration() || 0;
        updateTime(root, player.getCurrentTime() || 0, duration);
        setMutedState(root, getMuted());

        if (volume instanceof HTMLInputElement) {
          volume.value = String(getPlayerVolume() / 100);
        }
      },
      onStateChange: (event) => {
        const isPlaying = event.data === YT.PlayerState.PLAYING;
        setPlayingState(root, isPlaying);
        if (isPlaying) {
          setStartedState(root, true);
        }
        setStartingState(root, false);
        clearUiHideTimer(root);
        if (isPlaying) {
          scheduleUiHide(root);
        } else {
          setUiHiddenState(root, false);
        }
        setMutedState(root, getMuted());

        if (isPlaying) {
          const tick = () => {
            duration = player.getDuration() || duration;
            updateTime(root, player.getCurrentTime() || 0, duration);
            rafId = window.requestAnimationFrame(tick);
          };
          window.cancelAnimationFrame(rafId);
          tick();
        } else {
          window.cancelAnimationFrame(rafId);
          duration = player.getDuration() || duration;
          updateTime(root, player.getCurrentTime() || 0, duration);
        }
      },
    },
  });

  if (progress instanceof HTMLInputElement) {
    progress.addEventListener('input', () => {
      const nextDuration = player.getDuration() || duration;
      if (nextDuration > 0) {
        player.seekTo((Number(progress.value) / 100) * nextDuration, true);
      }
    });
  }

  if (volume instanceof HTMLInputElement) {
    volume.addEventListener('input', () => {
      const nextVolume = Number(volume.value);
      setPlayerVolume(nextVolume * 100);
      if (nextVolume === 0) {
        mutePlayer();
      } else {
        unmutePlayer();
      }
    });
  }

  return {
    play: () => player.playVideo(),
    pause: () => player.pauseVideo(),
    toggle: () => (player.getPlayerState() === YT.PlayerState.PLAYING ? player.pauseVideo() : player.playVideo()),
    seekBy: (delta) => {
      const nextDuration = player.getDuration() || duration;
      const currentTime = player.getCurrentTime() || 0;
      player.seekTo(Math.max(0, Math.min(nextDuration, currentTime + delta)), true);
    },
    setMuted: (muted) => {
      if (muted) {
        mutePlayer();
      } else {
        unmutePlayer();
      }
    },
    toggleMuted: () => {
      if (getMuted()) {
        unmutePlayer();
      } else {
        mutePlayer();
      }
    },
    setVolume: (nextVolume) => {
      setPlayerVolume(nextVolume * 100);
      if (nextVolume === 0) {
        mutePlayer();
      } else {
        unmutePlayer();
      }
    },
    getVolume: () => (getMuted() ? 0 : getPlayerVolume() / 100),
    isMuted: () => getMuted(),
    isPlaying: () => player.getPlayerState() === YT.PlayerState.PLAYING,
  };
}

async function createVimeoController(root, element) {
  const Vimeo = await loadVimeoApi();
  const progress = root.querySelector('[data-apache-video-progress]');
  const volume = root.querySelector('[data-apache-video-volume]');
  const playerOptions = {
    controls: false,
    autoplay: element.dataset.apacheVimeoAutoplay === 'true',
    muted: element.dataset.apacheVimeoMuted === 'true',
    loop: element.dataset.apacheVimeoLoop === 'true',
    byline: false,
    portrait: false,
    title: false,
    dnt: true,
    playsinline: true,
  };

  if (element.dataset.apacheVimeoUrl) {
    playerOptions.url = element.dataset.apacheVimeoUrl;
  }

  const player = new Vimeo.Player(element, playerOptions);
  let currentVolume = 1;
  let hasStartedPlayback = false;

  player.on('play', () => {
    setStartedState(root, true);
    setStartingState(root, true);
    setPlayingState(root, false);
    clearUiHideTimer(root);
  });
  player.on('pause', () => {
    setStartingState(root, false);
    setPlayingState(root, false);
    setBufferingState(root, false);
    clearUiHideTimer(root);
    setUiHiddenState(root, false);
  });
  player.on('ended', () => {
    setStartingState(root, false);
    setPlayingState(root, false);
    setBufferingState(root, false);
    clearUiHideTimer(root);
    setUiHiddenState(root, false);
  });
  player.on('bufferstart', () => {
    setBufferingState(root, true);
    clearUiHideTimer(root);
  });
  player.on('bufferend', async () => {
    setBufferingState(root, false);

    const isPlaying = await player.getPaused().catch(() => true);
    if (!isPlaying && hasStartedPlayback) {
      scheduleUiHide(root);
    }
  });
  player.on('timeupdate', (data) => {
    if (data.seconds > 0.35) {
      hasStartedPlayback = true;
      setStartedState(root, true);
      setStartingState(root, false);
      setPlayingState(root, true);
      clearUiHideTimer(root);
      if (!root.classList.contains('is-buffering')) {
        scheduleUiHide(root);
      }
    }
    updateTime(root, data.seconds, data.duration);
  });
  player.on('volumechange', (data) => {
    currentVolume = typeof data.volume === 'number' ? data.volume : currentVolume;
    setMutedState(root, currentVolume === 0);

    if (volume instanceof HTMLInputElement) {
      volume.value = String(currentVolume);
    }
  });
  player.getVolume().then((value) => {
    currentVolume = value;
    setMutedState(root, value === 0);
    if (volume instanceof HTMLInputElement) {
      volume.value = String(value);
    }
  }).catch(() => {});

  if (progress instanceof HTMLInputElement) {
    progress.addEventListener('input', async () => {
      const duration = await player.getDuration().catch(() => 0);
      if (duration > 0) {
        player.setCurrentTime((Number(progress.value) / 100) * duration).catch(() => {});
      }
    });
  }

  if (volume instanceof HTMLInputElement) {
    volume.addEventListener('input', () => {
      const nextVolume = Number(volume.value);
      currentVolume = nextVolume;
      player.setVolume(nextVolume).catch(() => {});
    });
  }

  return {
    play: () => player.play().catch(() => {}),
    pause: () => player.pause().catch(() => {}),
    toggle: async () => {
      const paused = await player.getPaused().catch(() => true);
      return paused ? player.play().catch(() => {}) : player.pause().catch(() => {});
    },
    seekBy: async (delta) => {
      const [currentTime, duration] = await Promise.all([
        player.getCurrentTime().catch(() => 0),
        player.getDuration().catch(() => 0),
      ]);
      player.setCurrentTime(Math.max(0, Math.min(duration, currentTime + delta))).catch(() => {});
    },
    setMuted: (muted) => {
      if (muted) {
        player.setVolume(0).catch(() => {});
      } else {
        player.setVolume(currentVolume > 0 ? currentVolume : 1).catch(() => {});
      }
    },
    toggleMuted: async () => {
      const volumeValue = await player.getVolume().catch(() => currentVolume);
      if (volumeValue > 0) {
        currentVolume = volumeValue;
        player.setVolume(0).catch(() => {});
      } else {
        player.setVolume(currentVolume > 0 ? currentVolume : 1).catch(() => {});
      }
    },
    setVolume: (nextVolume) => {
      currentVolume = nextVolume;
      player.setVolume(nextVolume).catch(() => {});
    },
    getVolume: async () => player.getVolume().catch(() => currentVolume),
    isMuted: async () => (await player.getVolume().catch(() => currentVolume)) === 0,
    isPlaying: async () => !(await player.getPaused().catch(() => true)),
  };
}

function createLazyVimeoController(root, element) {
  let controllerPromise = null;
  let resolvedController = null;
  let currentVolume = element.dataset.apacheVimeoMuted === 'true' ? 0 : 1;
  let initialMuted = element.dataset.apacheVimeoMuted === 'true';

  const ensureController = async () => {
    if (resolvedController) {
      return resolvedController;
    }

    if (!controllerPromise) {
      controllerPromise = createVimeoController(root, element)
        .then((controller) => {
          resolvedController = controller;
          controllerPromise = null;
          return controller;
        })
        .catch((error) => {
          controllerPromise = null;
          throw error;
        });
    }

    return controllerPromise;
  };

  return {
    play: () => {
      ensureController()
        .then((controller) => controller.play())
        .catch((error) => window.console.error(error));
    },
    pause: () => {
      if (resolvedController) {
        resolvedController.pause();
      }
    },
    toggle: () => {
      ensureController()
        .then((controller) => controller.toggle())
        .catch((error) => window.console.error(error));
    },
    seekBy: (delta) => {
      ensureController()
        .then((controller) => controller.seekBy(delta))
        .catch((error) => window.console.error(error));
    },
    setMuted: (muted) => {
      initialMuted = Boolean(muted);
      currentVolume = muted ? 0 : (currentVolume > 0 ? currentVolume : 1);

      ensureController()
        .then((controller) => controller.setMuted(muted))
        .catch((error) => window.console.error(error));
    },
    toggleMuted: () => {
      if (!resolvedController) {
        initialMuted = !initialMuted;
        currentVolume = initialMuted ? 0 : (currentVolume > 0 ? currentVolume : 1);
      }

      ensureController()
        .then((controller) => controller.toggleMuted())
        .catch((error) => window.console.error(error));
    },
    setVolume: (nextVolume) => {
      currentVolume = nextVolume;
      initialMuted = nextVolume === 0;

      ensureController()
        .then((controller) => controller.setVolume(nextVolume))
        .catch((error) => window.console.error(error));
    },
    getVolume: async () => {
      if (!resolvedController) {
        return currentVolume;
      }

      return resolvedController.getVolume();
    },
    isMuted: async () => {
      if (!resolvedController) {
        return initialMuted;
      }

      return resolvedController.isMuted();
    },
    isPlaying: async () => {
      if (!resolvedController) {
        return false;
      }

      return resolvedController.isPlaying();
    },
  };
}

function bindUi(root, controller) {
  const playButton = root.querySelector('[data-apache-video-play]');
  const surface = root.querySelector('[data-apache-video-surface]');
  const playToggle = root.querySelector('[data-apache-video-play-toggle]');
  const muteToggle = root.querySelector('[data-apache-video-mute]');
  const volume = root.querySelector('[data-apache-video-volume]');
  const fullscreen = root.querySelector('[data-apache-video-fullscreen]');
  const seekButtons = root.querySelectorAll('[data-apache-video-seek]');
  const embed = root.querySelector('[data-apache-video-embed]');

  const requestPlayback = async (mode = 'toggle') => {
    let isPlaying = false;

    try {
      isPlaying = await controller.isPlaying();
    } catch (error) {
      window.console.error(error);
    }

    if (!isPlaying) {
      setStartingState(root, true);
      setUiHiddenState(root, false);
    }

    if (mode === 'play') {
      controller.play();
      return;
    }

    controller.toggle();
  };

  playButton?.addEventListener('click', () => {
    requestPlayback('play');
  });

  surface?.addEventListener('click', () => {
    requestPlayback('toggle');
  });

  playToggle?.addEventListener('click', () => {
    requestPlayback('toggle');
  });
  muteToggle?.addEventListener('click', () => controller.toggleMuted());

  volume?.addEventListener('input', () => {
    controller.setVolume(Number(volume.value));
  });

  seekButtons.forEach((button) => {
    button.addEventListener('click', () => {
      controller.seekBy(Number(button.getAttribute('data-apache-video-seek') || '0'));
    });
  });

  fullscreen?.addEventListener('click', async () => {
    const target = embed instanceof HTMLElement ? embed : root;
    if (document.fullscreenElement) {
      await document.exitFullscreen().catch(() => {});
      return;
    }
    if (typeof target.requestFullscreen === 'function') {
      target.requestFullscreen().catch(() => {});
    }
  });
}

function stopVideoPlayback(root, controller) {
  clearUiHideTimer(root);
  setPlayingState(root, false);
  setStartingState(root, false);
  setBufferingState(root, false);
  setUiHiddenState(root, false);

  try {
    controller.pause();
  } catch (error) {
    window.console.error(error);
  }
}

export function initVideoPlayers(context = document) {
  const players = context.querySelectorAll('[data-apache-video]');

  players.forEach(async (root) => {
    if (!(root instanceof HTMLElement) || root.dataset.apacheVideoReady === 'true') {
      return;
    }

    root.dataset.apacheVideoReady = 'true';

    const type = root.dataset.videoType || '';
    let controller = null;

    try {
      if (type === 'self_hosted') {
        const video = root.querySelector('.apache-video__native');
        if (video instanceof HTMLVideoElement) {
          controller = createSelfHostedController(root, video);
        }
      } else if (type === 'youtube') {
        const iframe = root.querySelector('.apache-video__iframe');
        if (iframe instanceof HTMLIFrameElement) {
          controller = await createYouTubeController(root, iframe);
        }
      } else if (type === 'vimeo') {
        const element = root.querySelector('[data-apache-vimeo-player]');
        if (element instanceof HTMLElement) {
          controller = createLazyVimeoController(root, element);
        }
      }
    } catch (error) {
      window.console.error(error);
    }

    if (!controller) {
      return;
    }

    bindUi(root, controller);
    root.__apacheVideoController = controller;
    root.addEventListener('apache:video-stop', () => {
      stopVideoPlayback(root, controller);
    });
    setPlayingState(root, false);
    setStartedState(root, false);
    setStartingState(root, false);
    setBufferingState(root, false);
    setUiHiddenState(root, false);
    setMutedState(root, false);
    updateTime(root, 0, 0);

    try {
      if (typeof controller.isMuted === 'function') {
        const mutedState = await controller.isMuted();
        setMutedState(root, mutedState);
      }

      const volume = root.querySelector('[data-apache-video-volume]');
      if (volume instanceof HTMLInputElement && typeof controller.getVolume === 'function') {
        volume.value = String(await controller.getVolume());
      }
    } catch (error) {
      window.console.error(error);
    }
  });
}
