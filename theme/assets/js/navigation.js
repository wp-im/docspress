/* Docspress navigation: independent site menu, responsive sidebars, and toolbar geometry. */
(() => {
  'use strict';
  const root = document.documentElement;
  const body = document.body;
  const header = document.getElementById('docspress-header');
  const controls = document.getElementById('docspress-sidebar-controls');
  const main = document.getElementById('primary');
  const backdrop = document.getElementById('docspress-backdrop');
  const menu = document.getElementById('globalMenuPanel');
  const menuButton = document.getElementById('globalMenuBtn');
  const toolbar = document.getElementById('wpadminbar');
  const panels = [...document.querySelectorAll('[data-panel]')];
  const triggers = [...document.querySelectorAll('[data-panel-open]')];
  const narrowMenu = window.matchMedia('(max-width:1023px)');
  const leftMedia = window.matchMedia('(max-width:1023px)');
  const rightMedia = window.matchMedia('(max-width:1439px)');
  let active = null;
  let opener = null;
  let background = [];
  let geometryFrame = 0;
  let toolbarScrolls = false;
  let observer = null;
  let topStack = -1;
  let layoutWidth = window.innerWidth;
  const scrollRegions = [...document.querySelectorAll('.entry-content pre,.entry-content .wp-block-table,.entry-content .docspress-table-wrap,.entry-content .table-wrap,.entry-content>table')];

  root.classList.add('docspress-js');
  const isMenuCompact = () => narrowMenu.matches || root.classList.contains('docspress-menu-compact');
  const isDrawer = panel => panel.dataset.panel === 'left' ? leftMedia.matches : rightMedia.matches;
  const visible = element => element && element.getClientRects().length && getComputedStyle(element).visibility !== 'hidden';
  const focusable = panel => [...panel.querySelectorAll('a[href],button,input,select,textarea,summary,[tabindex]')]
    .filter(element => visible(element) && !element.disabled && !element.closest('[inert]') && element.getAttribute('tabindex') !== '-1');

  function restoreBackground() {
    background.forEach(([element, wasInert]) => element.toggleAttribute('inert', wasInert));
    background = [];
  }

  function silenceBackground(panel, parent = body) {
    [...parent.children].forEach(element => {
      if (element === panel || element === backdrop || /^(SCRIPT|STYLE|LINK)$/.test(element.tagName)) return;
      if (element.contains(panel)) {
        silenceBackground(panel, element);
      } else {
        background.push([element, element.hasAttribute('inert')]);
        element.setAttribute('inert', '');
      }
    });
  }

  function syncInteractive() {
    panels.forEach(panel => {
      const hidden = isDrawer(panel) && panel !== active;
      panel.toggleAttribute('inert', hidden);
      if (hidden) panel.setAttribute('aria-hidden', 'true');
      else panel.removeAttribute('aria-hidden');
    });
    if (menu) {
      const hidden = isMenuCompact() && active !== menu;
      menu.toggleAttribute('inert', hidden);
      if (hidden) menu.setAttribute('aria-hidden', 'true');
      else menu.removeAttribute('aria-hidden');
    }
  }

  function close(restoreFocus = false) {
    const previous = opener;
    if (active) {
      active.classList.remove('is-open');
      active.removeAttribute('role');
      active.removeAttribute('aria-modal');
      active.removeAttribute('aria-labelledby');
    }
    restoreBackground();
    active = null;
    opener = null;
    body.classList.remove('docspress-modal-open');
    if (backdrop) backdrop.hidden = true;
    triggers.forEach(button => button.setAttribute('aria-expanded', 'false'));
    menuButton?.setAttribute('aria-expanded', 'false');
    syncInteractive();
    if (restoreFocus) (visible(previous) ? previous : main)?.focus({preventScroll: true});
  }

  function openPanel(panel, button) {
    if (!panel || !isDrawer(panel)) return;
    close();
    active = panel;
    opener = button;
    panel.removeAttribute('inert');
    panel.removeAttribute('aria-hidden');
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-modal', 'true');
    panel.setAttribute('aria-labelledby', panel.id + '-title');
    panel.classList.add('is-open');
    button.setAttribute('aria-expanded', 'true');
    silenceBackground(panel);
    body.classList.add('docspress-modal-open');
    if (backdrop) backdrop.hidden = false;
    panel.querySelector('[data-panel-close]')?.focus({preventScroll: true});
  }

  triggers.forEach(button => button.addEventListener('click', () => {
    const panel = document.getElementById(button.getAttribute('aria-controls'));
    if (active === panel) close(true);
    else openPanel(panel, button);
  }));
  document.querySelectorAll('[data-panel-close]').forEach(button => button.addEventListener('click', () => close(true)));
  backdrop?.addEventListener('click', () => close(true));
  menuButton?.addEventListener('click', () => {
    if (active === menu) {
      close(true);
      return;
    }
    close();
    active = menu;
    opener = menuButton;
    menu.classList.add('is-open');
    menuButton.setAttribute('aria-expanded', 'true');
    syncInteractive();
    focusable(menu)[0]?.focus({preventScroll: true});
  });

  document.addEventListener('pointerdown', event => {
    if (active === menu && !header.contains(event.target)) close();
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && active) {
      event.preventDefault();
      close(true);
      return;
    }
    if (event.key !== 'Tab' || !active || active === menu) return;
    const items = focusable(active);
    if (!items.length) return;
    const first = items[0];
    const last = items[items.length - 1];
    if (!active.contains(document.activeElement) || (event.shiftKey && document.activeElement === first) || (!event.shiftKey && document.activeElement === last)) {
      event.preventDefault();
      (event.shiftKey ? last : first).focus();
    }
  });

  document.querySelectorAll('.tree-toggle[data-tree-key]').forEach(button => {
    button.addEventListener('click', () => {
      const open = button.getAttribute('aria-expanded') !== 'true';
      button.setAttribute('aria-expanded', String(open));
      button.closest('.tree-node')?.classList.toggle('is-open', open);
    });
  });

  document.addEventListener('click', event => {
    const link = event.target.closest('a[href]');
    if (!link) return;
    const samePage = link.origin === location.origin && link.pathname === location.pathname && link.search === location.search && link.hash;
    if (active?.contains(link)) close();
    if (!samePage) return;
    let id;
    try { id = decodeURIComponent(link.hash.slice(1)); } catch { return; }
    const target = document.getElementById(id);
    if (target) {
      requestAnimationFrame(() => {
        if (!target.hasAttribute('tabindex')) target.setAttribute('tabindex', '-1');
        target.focus({preventScroll: true});
      });
    }
  });

  function updateTocObserver(offset) {
    const links = [...document.querySelectorAll('.docspress-toc a[href^="#"]')];
    if (!links.length || !('IntersectionObserver' in window)) return;
    const entries = links.map(link => {
      try { return [link, document.getElementById(decodeURIComponent(link.hash.slice(1)))]; } catch { return [link, null]; }
    }).filter(([, heading]) => heading);
    observer?.disconnect();
    observer = new IntersectionObserver(() => {
      const current = entries.filter(([, heading]) => heading.getBoundingClientRect().top <= offset + 56).at(-1) || entries[0];
      entries.forEach(([link]) => {
        link.classList.toggle('active', current?.[0] === link);
        if (current?.[0] === link) link.setAttribute('aria-current', 'location');
        else link.removeAttribute('aria-current');
      });
    }, {rootMargin: '-' + Math.round(offset) + 'px 0px -55% 0px', threshold: 0});
    entries.forEach(([, heading]) => observer.observe(heading));
  }

  function updateGeometry() {
    geometryFrame = 0;
    let bar = 0;
    if (toolbar && visible(toolbar)) {
      const rect = toolbar.getBoundingClientRect();
      const style = getComputedStyle(toolbar);
      toolbarScrolls = style.position !== 'fixed';
      if (rect.top <= 0) bar = Math.max(0, Math.min(rect.height, rect.bottom));
      else if (!toolbarScrolls) bar = Math.max(0, rect.bottom);
    }
    const h = header?.getBoundingClientRect().height || 0;
    const local = controls && getComputedStyle(controls).display !== 'none' ? controls.getBoundingClientRect().height : 0;
    root.style.setProperty('--dp-bar-visible', bar + 'px');
    root.style.setProperty('--dp-header-h', h + 'px');
    root.style.setProperty('--dp-local-h', local + 'px');
    const total = Math.round(bar + h + local);
    if (total !== topStack) {
      topStack = total;
      updateTocObserver(total);
    }
  }

  function queueGeometry() {
    if (!geometryFrame) geometryFrame = requestAnimationFrame(updateGeometry);
  }

  function resize() {
    const widthChanged = window.innerWidth !== layoutWidth;
    layoutWidth = window.innerWidth;
    if (widthChanged && active) close(true);
    root.classList.remove('docspress-menu-compact');
    if (!narrowMenu.matches && header && header.querySelector('.header-inner').scrollWidth > header.clientWidth + 1) {
      root.classList.add('docspress-menu-compact');
    }
    if (!active || active === menu || widthChanged) syncInteractive();
    scrollRegions.forEach(region => {
      const overflowing = region.scrollWidth > region.clientWidth + 1;
      if (overflowing && !region.hasAttribute('tabindex')) {
        region.setAttribute('tabindex', '0');
        region.dataset.docspressScrollable = 'true';
        if (!region.hasAttribute('aria-label') && window.docspressNavigation) {
          region.setAttribute('aria-label', window.docspressNavigation.scrollRegion);
          region.dataset.docspressScrollLabel = 'true';
        }
      } else if (!overflowing && region.dataset.docspressScrollable) {
        region.removeAttribute('tabindex');
        if (region.dataset.docspressScrollLabel) region.removeAttribute('aria-label');
        delete region.dataset.docspressScrollLabel;
        delete region.dataset.docspressScrollable;
      }
    });
    updateGeometry();
  }

  window.addEventListener('resize', resize, {passive: true});
  window.addEventListener('scroll', () => { if (toolbarScrolls) queueGeometry(); }, {passive: true});
  window.addEventListener('pageshow', () => { close(!!active); resize(); });
  window.addEventListener('load', () => { resize(); queueGeometry(); });
  if ('ResizeObserver' in window) {
    const sizes = new ResizeObserver(queueGeometry);
    [header, controls, toolbar].filter(Boolean).forEach(element => sizes.observe(element));
  }
  resize();
})();
