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

    const normalize = (value) => value
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .toLowerCase();

    const setTabCount = (type, count) => {
        const tab = tabs.find((item) => item.dataset.tsTab === type);
        const badge = tab?.querySelector('[data-ts-count]');

        if (badge) {
            badge.textContent = count;
        }
    };

    const updateCards = () => {
        const query = normalize(search?.value || '');
        const counts = {};
        let totalVisible = 0;

        contents.forEach((content) => {
            const type = content.dataset.tsContent;
            const cards = [...content.querySelectorAll('[data-ts-card]')];
            let visible = 0;

            cards.forEach((card) => {
                const matches = !query || normalize(card.dataset.tsSearchText || '').includes(query);
                card.hidden = !matches;

                if (matches) {
                    visible += 1;
                }
            });

            counts[type] = visible;
            totalVisible += visible;
            setTabCount(type, visible);
        });

        if (query && counts[activeType] === 0) {
            const target = Object.keys(counts).find((type) => counts[type] > 0);

            if (target) {
                activateTab(target, false);
            }
        }

        if (empty) {
            empty.hidden = totalVisible > 0;
        }
    };

    const activateTab = (type, refresh = true) => {
        activeType = type;

        tabs.forEach((tab) => {
            const isActive = tab.dataset.tsTab === type;
            tab.classList.toggle('is-active', isActive);
            tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });

        contents.forEach((content) => {
            content.classList.toggle('is-active', content.dataset.tsContent === type);
        });

        if (refresh) {
            updateCards();
        }
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => activateTab(tab.dataset.tsTab));
    });

    search?.addEventListener('input', updateCards);
    search?.addEventListener('search', updateCards);
    updateCards();
});
