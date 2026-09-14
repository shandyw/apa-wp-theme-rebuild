export function initBrowserClasses(root = document.documentElement) {
  const userAgent = window.navigator.userAgent;
  const userAgentLower = userAgent.toLowerCase();
  const classes = [];

  if (/(iphone|ipod|ipad)/.test(userAgentLower)) {
    classes.push('ios', 'mobile');
  }

  if (userAgent.includes('MSIE') || userAgent.includes('Trident/')) {
    classes.push('ie');
  } else if (userAgent.includes('Chrome') || userAgent.includes('CriOS')) {
    classes.push('chrome');
  } else if (userAgent.includes('Firefox') || userAgent.includes('FxiOS')) {
    classes.push('firefox');
  } else if (userAgent.includes('Safari')) {
    classes.push('safari');
  } else if (userAgent.includes('Opera') || userAgent.includes('OPR/')) {
    classes.push('opera');
  }

  if (classes.length > 0) {
    root.classList.add(...classes);
  }
}
