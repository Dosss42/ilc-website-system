const PHAddress = (() => {
    const BASE = '/Philippines-list-of-Region-Province-City-Municipality-and-Barangay-master/json/';
    let regions = null, provinces = null, cities = null, barangays = null;
    let preloadPromise = null, brgyPromise = null;

    async function fetchJSON(file) {
        const r = await fetch(BASE + file);
        const d = await r.json();
        return d.RECORDS;
    }

    function preload() {
        if (!preloadPromise)
            preloadPromise = Promise.all([
                fetchJSON('refregion.json'),
                fetchJSON('refprovince.json'),
                fetchJSON('refcitymun.json'),
            ]).then(([r, p, c]) => { regions = r; provinces = p; cities = c; });
        return preloadPromise;
    }

    function loadBrgys() {
        if (!brgyPromise)
            brgyPromise = fetchJSON('refbrgy.json').then(b => { barangays = b; });
        return brgyPromise;
    }

    function fill(el, items, textKey, codeKey, placeholder) {
        el.innerHTML = `<option value="">${placeholder}</option>`;
        items.forEach(item => {
            const o = document.createElement('option');
            o.value = item[textKey];
            o.textContent = item[textKey];
            if (codeKey) o.dataset.code = item[codeKey];
            el.appendChild(o);
        });
        el.disabled = false;
    }

    function clear(el, placeholder) {
        el.innerHTML = `<option value="">${placeholder}</option>`;
        el.disabled = true;
    }

    function getCode(el) {
        const o = el.options[el.selectedIndex];
        return o ? (o.dataset.code || '') : '';
    }

    function initCascade({ region: rId, province: pId, city: cId, barangay: bId }) {
        const rEl = document.getElementById(rId);
        const pEl = document.getElementById(pId);
        const cEl = document.getElementById(cId);
        const bEl = document.getElementById(bId);

        [pEl, cEl, bEl].forEach(el => { el.disabled = true; });

        preload().then(() => fill(rEl, regions, 'regDesc', 'regCode', 'Select Region'));

        rEl.addEventListener('change', () => {
            clear(pEl, 'Select Province');
            clear(cEl, 'Select City/Municipality');
            clear(bEl, 'Select Barangay');
            const c = getCode(rEl);
            if (c) fill(pEl, provinces.filter(p => p.regCode === c), 'provDesc', 'provCode', 'Select Province');
        });

        pEl.addEventListener('change', () => {
            clear(cEl, 'Select City/Municipality');
            clear(bEl, 'Select Barangay');
            const c = getCode(pEl);
            if (c) fill(cEl, cities.filter(x => x.provCode === c), 'citymunDesc', 'citymunCode', 'Select City/Municipality');
        });

        cEl.addEventListener('change', async () => {
            clear(bEl, 'Select Barangay');
            const c = getCode(cEl);
            if (!c) return;
            if (!barangays) {
                bEl.innerHTML = '<option value="">Loading barangays...</option>';
                bEl.disabled = true;
                await loadBrgys();
            }
            fill(bEl, barangays.filter(b => b.citymunCode === c), 'brgyDesc', null, 'Select Barangay');
        });
    }

    // Pre-populate cascading selects with existing values (for edit modals).
    // Reverse-looks up region from province name to reconstruct the cascade.
    async function setValues({ region: rId, province: pId, city: cId, barangay: bId }, vals) {
        await preload();
        const rEl = document.getElementById(rId);
        const pEl = pId ? document.getElementById(pId) : null;
        const cEl = cId ? document.getElementById(cId) : null;
        const bEl = bId ? document.getElementById(bId) : null;

        let regCode = null, provCode = null, cityCode = null;
        const up = s => (s || '').toUpperCase().trim();

        if (vals.province) {
            const p = provinces.find(x => x.provDesc === up(vals.province));
            if (p) { regCode = p.regCode; provCode = p.provCode; }
        }
        if (vals.region && !regCode) {
            const r = regions.find(x => x.regDesc === up(vals.region));
            if (r) regCode = r.regCode;
        }

        fill(rEl, regions, 'regDesc', 'regCode', 'Select Region');
        if (regCode) {
            for (const o of rEl.options) if (o.dataset.code === regCode) { o.selected = true; break; }
        }

        if (pEl && regCode) {
            const fp = provinces.filter(p => p.regCode === regCode);
            fill(pEl, fp, 'provDesc', 'provCode', 'Select Province');
            if (provCode) {
                for (const o of pEl.options) if (o.dataset.code === provCode) { o.selected = true; break; }
            }
        }

        if (cEl && provCode) {
            const fc = cities.filter(c => c.provCode === provCode);
            fill(cEl, fc, 'citymunDesc', 'citymunCode', 'Select City/Municipality');
            if (vals.city) {
                const found = fc.find(c => c.citymunDesc === up(vals.city));
                if (found) {
                    cityCode = found.citymunCode;
                    for (const o of cEl.options) if (o.dataset.code === cityCode) { o.selected = true; break; }
                }
            }
        }

        if (bEl && cityCode) {
            if (!barangays) {
                bEl.innerHTML = '<option value="">Loading barangays...</option>';
                bEl.disabled = true;
                await loadBrgys();
            }
            const fb = barangays.filter(b => b.citymunCode === cityCode);
            fill(bEl, fb, 'brgyDesc', null, 'Select Barangay');
            if (vals.barangay) {
                for (const o of bEl.options) {
                    if (o.value === vals.barangay || o.value.toUpperCase() === up(vals.barangay)) {
                        o.selected = true;
                        break;
                    }
                }
            }
        }
    }

    return { initCascade, setValues, preload };
})();
