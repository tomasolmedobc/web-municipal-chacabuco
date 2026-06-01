document.addEventListener('DOMContentLoaded', () => {
    const panel = document.querySelector('[data-ts-panel]');

    if (!panel) {
        return;
    }

    const tabs = [...panel.querySelectorAll('[data-ts-tab]')];
    const contents = [...panel.querySelectorAll('[data-ts-content]')];
    const search = panel.querySelector('[data-ts-search]');
    const empty = panel.querySelector('[data-ts-empty]');
    let activeType = 'tramites';

    const normalize = (value) => value.toString().trim().toLowerCase();

    const updateCards = () => {
        const query = normalize(search?.value || '');
        const activeContent = contents.find((content) => content.dataset.tsContent === activeType);
        const cards = [...(activeContent?.querySelectorAll('[data-ts-card]') || [])];
        let visible = 0;

        cards.forEach((card) => {
            const matches = !query || normalize(card.dataset.tsSearchText || '').includes(query);
            card.hidden = !matches;

            if (matches) {
                visible += 1;
            }
        });

        if (empty) {
            empty.hidden = visible > 0;
        }
    };

    const activateTab = (type) => {
        activeType = type;

        tabs.forEach((tab) => {
            const isActive = tab.dataset.tsTab === type;
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        contents.forEach((content) => {
            content.classList.toggle('is-active', content.dataset.tsContent === type);
        });

        updateCards();
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => activateTab(tab.dataset.tsTab));
    });

    search?.addEventListener('input', updateCards);
    updateCards();
});
