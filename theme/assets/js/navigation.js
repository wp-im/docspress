const body = document.body;
const backdrop = document.getElementById('drawerBackdrop');
const chapterDrawer = document.getElementById('chapterDrawer');
const tocDrawer = document.getElementById('tocDrawer');
const globalPanel = document.getElementById('globalMenuPanel');
const globalBtn = document.getElementById('globalMenuBtn');
const chapterBtn = document.getElementById('chapterMenuBtn');
const tocBtn = document.getElementById('tocMenuBtn');
let panelOpener = null;

function setPanelInteractive(panel, interactive){
  if(!panel) return;
  panel.setAttribute('aria-hidden', interactive ? 'false' : 'true');
  panel.toggleAttribute('inert', !interactive);
}

function getActivePanel(){
  return [chapterDrawer, tocDrawer, globalPanel].find(panel => panel?.classList.contains('open')) || null;
}

function getFocusable(panel){
  if(!panel) return [];
  return [...panel.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])')]
    .filter(element => element.getAttribute('aria-hidden') !== 'true');
}

setPanelInteractive(chapterDrawer, false);
setPanelInteractive(tocDrawer, false);
setPanelInteractive(globalPanel, false);

function resetPanels(restoreFocus = false){
  chapterDrawer?.classList.remove('open');
  setPanelInteractive(chapterDrawer, false);
  tocDrawer?.classList.remove('open');
  setPanelInteractive(tocDrawer, false);
  globalPanel?.classList.remove('open');
  setPanelInteractive(globalPanel, false);
  backdrop?.classList.remove('visible','below-header');
  body.classList.remove('no-scroll');
  globalBtn?.setAttribute('aria-expanded','false');
  chapterBtn?.setAttribute('aria-expanded','false');
  tocBtn?.setAttribute('aria-expanded','false');
  if(restoreFocus && panelOpener){
    panelOpener.focus();
  }
  panelOpener = null;
}

function openChapter(){
  resetPanels();
  panelOpener = chapterBtn;
  chapterDrawer?.classList.add('open');
  setPanelInteractive(chapterDrawer, true);
  chapterBtn?.setAttribute('aria-expanded','true');
  backdrop?.classList.add('visible');
  body.classList.add('no-scroll');
  chapterDrawer?.querySelector('a,button')?.focus();
}

function openToc(){
  resetPanels();
  panelOpener = tocBtn;
  tocDrawer?.classList.add('open');
  setPanelInteractive(tocDrawer, true);
  tocBtn?.setAttribute('aria-expanded','true');
  backdrop?.classList.add('visible','below-header');
  body.classList.add('no-scroll');
  tocDrawer?.querySelector('a,button')?.focus();
}

chapterBtn?.addEventListener('click', openChapter);
tocBtn?.addEventListener('click', openToc);

globalBtn?.addEventListener('click', () => {
  const willOpen = !globalPanel?.classList.contains('open');
  resetPanels();
  if(willOpen && globalPanel){
    panelOpener = globalBtn;
    globalPanel.classList.add('open');
    setPanelInteractive(globalPanel, true);
    body.classList.add('no-scroll');
    globalBtn.setAttribute('aria-expanded','true');
    globalPanel.querySelector('a,button')?.focus();
  }
});

backdrop?.addEventListener('click', () => resetPanels(true));
document.querySelectorAll('[data-close]').forEach(btn => btn.addEventListener('click', () => resetPanels(true)));
document.querySelectorAll('.chapter-drawer a, .toc-drawer a, .global-menu-links a').forEach(a => {
  a.addEventListener('click', () => setTimeout(resetPanels, 20));
});
document.addEventListener('keydown', e => {
  if(e.key === 'Escape'){
    resetPanels(true);
    return;
  }
  if(e.key !== 'Tab') return;
  const panel = getActivePanel();
  const focusable = getFocusable(panel);
  if(!focusable.length) return;
  const first = focusable[0];
  const last = focusable[focusable.length - 1];
  if(!panel.contains(document.activeElement)){
    e.preventDefault();
    (e.shiftKey ? last : first).focus();
  } else if(e.shiftKey && document.activeElement === first){
    e.preventDefault();
    last.focus();
  } else if(!e.shiftKey && document.activeElement === last){
    e.preventDefault();
    first.focus();
  }
});

const phoneMedia = window.matchMedia('(max-width:640px)');
const shellMedia = window.matchMedia('(max-width:980px)');
phoneMedia.addEventListener('change', resetPanels);
shellMedia.addEventListener('change', resetPanels);

// The arrow only changes expansion. The adjacent title remains a normal link.
document.querySelectorAll('.tree-toggle[data-tree-key]').forEach(toggle => {
  toggle.addEventListener('click', () => {
    const key = toggle.dataset.treeKey;
    const nextState = toggle.getAttribute('aria-expanded') !== 'true';
    document.querySelectorAll(`.tree-toggle[data-tree-key="${key}"]`).forEach(peer => {
      peer.setAttribute('aria-expanded', String(nextState));
    });
    document.querySelectorAll(`.tree-node[data-tree-node="${key}"]`).forEach(node => {
      node.classList.toggle('is-open', nextState);
    });
  });
});

const sections = [...document.querySelectorAll('h2.docspress-section[id], h3.docspress-subsection[id]')];
const tocLinks = [...document.querySelectorAll('.right-toc a, .toc-drawer-body a')];
if(sections.length && tocLinks.length && 'IntersectionObserver' in window){
  const observer = new IntersectionObserver(entries => {
    const visible = entries.filter(e => e.isIntersecting)
      .sort((a,b) => a.boundingClientRect.top - b.boundingClientRect.top)[0];
    if(!visible) return;
    tocLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + visible.target.id));
  }, {rootMargin:'-20% 0px -68% 0px', threshold:0});
  sections.forEach(section => observer.observe(section));
}
