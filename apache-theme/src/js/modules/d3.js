import * as d3 from "d3";

const REGION_GEO_BOUNDS = {
  US: {
    minLat: 24.396308,
    maxLat: 49.384358,
    minLng: -124.848974,
    maxLng: -66.885444,
  },
  UK: {
    minLat: 49.864741,
    maxLat: 60.860699,
    minLng: -8.649357,
    maxLng: 1.763338,
  },
  EG: {
    minLat: 21.999734,
    maxLat: 31.667334,
    minLng: 24.70007,
    maxLng: 36.898068,
  },
  SR: {
    minLat: 1.831145,
    maxLat: 6.004546,
    minLng: -58.070833,
    maxLng: -53.977493,
  },
};

export function initMaps(context = document) {
  const maps = context.querySelectorAll("[data-home-map]");

  maps.forEach((map) => {
    if (map.dataset.mapInitialized === "true") return;

    const svg = map.querySelector("svg");
    const tooltip = map.querySelector("[data-map-tooltip]");
    const tooltipCaption = map.querySelector("[data-map-tooltip-caption]");

    if (!svg) return;

    let locations = [];

    try {
      locations = JSON.parse(map.dataset.locations || "[]");
    } catch (error) {
      console.warn("Invalid home map location data", error);
      return;
    }

    if (!locations.length) return;

    map.dataset.mapInitialized = "true";

    const svgSelection = d3.select(svg);
    const regionNodes = svg.querySelectorAll("[data-region]");
    const regionMap = new Map();

    regionNodes.forEach((regionNode) => {
      const regionCode = regionNode.getAttribute("data-region");

      regionNode.classList.add("home-map__region");

      if (regionCode) {
        const existingRegions = regionMap.get(regionCode) || [];

        existingRegions.push(regionNode);
        regionMap.set(regionCode, existingRegions);
      }
    });

    const markerLayer = svgSelection
      .append("g")
      .attr("class", "home-map__markers");

    const clearActiveLocation = () => {
      map.querySelectorAll(".is-active").forEach((el) => {
        el.classList.remove("is-active");
        el.style.strokeDasharray = "";
        el.style.strokeDashoffset = "";
      });

      if (tooltip) {
        tooltip.hidden = true;
      }
    };

    const setActiveLocation = (location, markerNode) => {
      clearActiveLocation();

      const regions = location.region ? regionMap.get(location.region) || [] : [];

      regions.forEach((region) => {
        const length = region.getTotalLength?.();

        region.classList.add("is-active");

        if (length) {
          region.style.strokeDasharray = length;
          region.style.strokeDashoffset = length;

          requestAnimationFrame(() => {
            region.style.strokeDashoffset = "0";
          });
        }
      });

      markerNode.classList.add("is-active");

      if (tooltip && tooltipCaption) {
        tooltipCaption.textContent = location.caption || "";
        tooltip.hidden = false;

        const markerRect = markerNode.getBoundingClientRect();
        const tooltipOffsetParent = tooltip.offsetParent || map;
        const offsetParentRect = tooltipOffsetParent.getBoundingClientRect();

        tooltip.style.left = `${markerRect.left - offsetParentRect.left + markerRect.width / 2}px`;
        tooltip.style.top = `${markerRect.top - offsetParentRect.top}px`;
      }
    };

    const getRegionCenter = (regions) => {
      if (!regions.length) return null;

      let minX = Infinity;
      let minY = Infinity;
      let maxX = -Infinity;
      let maxY = -Infinity;

      regions.forEach((region) => {
        const box = region.getBBox?.();

        if (!box) return;

        minX = Math.min(minX, box.x);
        minY = Math.min(minY, box.y);
        maxX = Math.max(maxX, box.x + box.width);
        maxY = Math.max(maxY, box.y + box.height);
      });

      if (
        !Number.isFinite(minX) ||
        !Number.isFinite(minY) ||
        !Number.isFinite(maxX) ||
        !Number.isFinite(maxY)
      ) {
        return null;
      }

      return [
        minX + (maxX - minX) / 2,
        minY + (maxY - minY) / 2,
      ];
    };

    const getRegionBounds = (regions) => {
      if (!regions.length) return null;

      let minX = Infinity;
      let minY = Infinity;
      let maxX = -Infinity;
      let maxY = -Infinity;

      regions.forEach((region) => {
        const box = region.getBBox?.();

        if (!box) return;

        minX = Math.min(minX, box.x);
        minY = Math.min(minY, box.y);
        maxX = Math.max(maxX, box.x + box.width);
        maxY = Math.max(maxY, box.y + box.height);
      });

      if (
        !Number.isFinite(minX) ||
        !Number.isFinite(minY) ||
        !Number.isFinite(maxX) ||
        !Number.isFinite(maxY)
      ) {
        return null;
      }

      return { minX, minY, maxX, maxY };
    };

    const getMapBounds = () => {
      let minX = Infinity;
      let minY = Infinity;
      let maxX = -Infinity;
      let maxY = -Infinity;

      regionNodes.forEach((regionNode) => {
        const box = regionNode.getBBox?.();

        if (!box) return;

        minX = Math.min(minX, box.x);
        minY = Math.min(minY, box.y);
        maxX = Math.max(maxX, box.x + box.width);
        maxY = Math.max(maxY, box.y + box.height);
      });

      if (
        !Number.isFinite(minX) ||
        !Number.isFinite(minY) ||
        !Number.isFinite(maxX) ||
        !Number.isFinite(maxY)
      ) {
        const viewBox = svg.viewBox?.baseVal;

        return {
          minX: 0,
          minY: 0,
          maxX: viewBox?.width || svg.width?.baseVal?.value || 1642,
          maxY: viewBox?.height || svg.height?.baseVal?.value || 1026,
        };
      }

      return { minX, minY, maxX, maxY };
    };

    const mapBounds = getMapBounds();
    const coordinateProjection = d3
      .geoNaturalEarth1()
      .fitExtent(
        [
          [mapBounds.minX, mapBounds.minY],
          [mapBounds.maxX, mapBounds.maxY],
        ],
        { type: "Sphere" },
      );

    const getLocationCoordinates = (location) => {
      const lat = Number.parseFloat(location.lat);
      const lng = Number.parseFloat(location.lng);
      const regionCode = (location.region || "").toUpperCase();
      const regions = regionCode ? regionMap.get(regionCode) || [] : [];
      const geoBounds = regionCode ? REGION_GEO_BOUNDS[regionCode] : null;
      const regionBounds = getRegionBounds(regions);

      if (
        Number.isFinite(lat) &&
        Number.isFinite(lng) &&
        geoBounds &&
        regionBounds
      ) {
        const lngRatio = (lng - geoBounds.minLng) / (geoBounds.maxLng - geoBounds.minLng);
        const latRatio = (geoBounds.maxLat - lat) / (geoBounds.maxLat - geoBounds.minLat);
        const clampedLngRatio = Math.min(1, Math.max(0, lngRatio));
        const clampedLatRatio = Math.min(1, Math.max(0, latRatio));

        return [
          regionBounds.minX + (regionBounds.maxX - regionBounds.minX) * clampedLngRatio,
          regionBounds.minY + (regionBounds.maxY - regionBounds.minY) * clampedLatRatio,
        ];
      }

      if (Number.isFinite(lat) && Number.isFinite(lng)) {
        const projected = coordinateProjection([lng, lat]);

        if (Array.isArray(projected) && projected.every((value) => Number.isFinite(value))) {
          return projected;
        }
      }

      return getRegionCenter(regions);
    };

    locations.forEach((location) => {
      const coords = getLocationCoordinates(location);

      if (!coords) return;

      const [x, y] = coords;

      const marker = markerLayer
        .append("a")
        .attr("class", "home-map__marker")
        .attr("href", location.url || "#")
        .attr("aria-label", location.title || "Map location")
        .attr("data-region", location.region || "")
        .attr("transform", `translate(${x}, ${y})`);

      if (location.target) {
        marker.attr("target", location.target);
      }

      marker
        .append("circle")
        .attr("class", "home-map__marker-dot")
        .attr("r", 6);

      marker
        .append("circle")
        .attr("class", "home-map__marker-pulse")
        .attr("r", 12);

      marker
        .append("text")
        .attr("class", "home-map__marker-title")
        .attr("x", 0)
        .attr("y", -18)
        .attr("text-anchor", "middle")
        .text(location.title || "");

      marker
        .on("mouseenter focus", function () {
          setActiveLocation(location, this);
        })
        .on("mouseleave blur", function () {
          clearActiveLocation();
        });
    });
  });
}
