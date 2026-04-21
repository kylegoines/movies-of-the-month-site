export class CollectionActivityLayout {
  constructor(sidebars) {
    this.sidebars = [...sidebars];
    this.minimumViewport = window.matchMedia('(min-width: 64rem)');
    this.floatGap = 40;
    this.floatWidth = 300;
  }

  updateSidebar(sidebar) {
    const shell = sidebar.closest('[data-collection-activity-shell]');
    const content = shell?.querySelector('[data-collection-activity-content]');

    if (!shell || !content) {
      return;
    }

    if (!this.minimumViewport.matches) {
      sidebar.dataset.layout = 'embedded';
      return;
    }

    const contentRect = content.getBoundingClientRect();
    const availableRight = window.innerWidth - contentRect.right;
    const hasRoomToFloat = availableRight >= this.floatWidth + this.floatGap;

    sidebar.dataset.layout = hasRoomToFloat ? 'floating' : 'embedded';
  }

  updateAll = () => {
    this.sidebars.forEach((sidebar) => this.updateSidebar(sidebar));
  };

  init() {
    if (this.sidebars.length === 0) {
      return;
    }

    this.updateAll();
    window.addEventListener('resize', this.updateAll);
  }
}
