(() => {
    const CONFIG = window.APP_CONFIG || {
        transportApi: '/app-api',
        wisataProxyApi: '/app-api/wisata',
    };

    const ORIGIN_PRESETS = {
        medan: { label: 'Medan, Sumatera Utara', lat: 3.5952, lng: 98.6722 },
        jakarta: { label: 'Jakarta (DKI Jakarta)', lat: -6.2088, lng: 106.8456 },
        palembang: { label: 'Palembang, Sumsel', lat: -2.9761, lng: 104.7754 },
        surabaya: { label: 'Surabaya, Jawa Timur', lat: -7.2575, lng: 112.7521 },
    };

    const FALLBACK_IMG =
        'data:image/svg+xml;charset=UTF-8,' +
        encodeURIComponent(
            '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="220" viewBox="0 0 320 220">' +
            '<rect width="320" height="220" fill="#DCE6EE"/>' +
            '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#6B7887" font-family="Inter,sans-serif" font-size="14" font-weight="600">Foto Wisata</text>' +
            '</svg>'
        );

    const state = {
        screen: 'landing',
        originKey: 'medan',
        origin: { ...ORIGIN_PRESETS.medan },
        kota: '',
        kategori: '',
        kataKunci: '',
        tripType: 'roundtrip',
        halaman: 1,
        totalHalaman: 1,
        totalData: 0,
        destinasiList: [],
        selectedWisata: null,
        lokasiSimpul: [],
    };

    let map = null;
    let markerLayer = null;
    let searchTimer = null;

    // ==================== DOM ELEMENTS ====================
    const el = {
        btnLoadMorePopular: document.getElementById('btn-load-more-popular'),
        screenLanding: document.getElementById('screen-landing'),
        screenMap: document.getElementById('screen-map'),
        screenDetail: document.getElementById('screen-detail'),

        btnOpenOrigin: document.getElementById('btn-open-origin-modal'),
        landingOriginLabel: document.getElementById('landing-origin-label'),
        btnStart: document.getElementById('btn-start'),

        btnBackLanding: document.getElementById('btn-back-landing'),
        searchInput: document.getElementById('search-input'),
        kotaSelect: document.getElementById('kota-select'),
        categoryChips: document.getElementById('category-chips'),
        mapToast: document.getElementById('map-status-toast'),

        previewCard: document.getElementById('wisata-preview-card'),
        previewImg: document.getElementById('preview-img'),
        previewTitle: document.getElementById('preview-title'),
        previewAddress: document.getElementById('preview-address'),
        previewMeta: document.getElementById('preview-meta'),
        btnToDetail: document.getElementById('btn-to-detail'),

        btnBackMap: document.getElementById('btn-back-map'),
        selectedBanner: document.getElementById('selected-wisata-banner'),
        bannerImg: document.getElementById('banner-img'),
        bannerCategory: document.getElementById('banner-category'),
        bannerTitle: document.getElementById('banner-title'),
        bannerSub: document.getElementById('banner-sub'),
        bannerTicket: document.getElementById('banner-ticket'),
        bannerLink: document.getElementById('banner-link'),

        popularKotaLabel: document.getElementById('popular-kota-label'),
        popularGrid: document.getElementById('popular-grid'),

        originSelectDetail: document.getElementById('origin-select-detail'),
        btnUseGps: document.getElementById('btn-use-gps'),
        btnTripRoundtrip: document.getElementById('btn-trip-roundtrip'),
        btnTripOneway: document.getElementById('btn-trip-oneway'),
        transportSummary: document.getElementById('transport-summary'),
        transportSegments: document.getElementById('transport-segments'),

        tarifForm: document.getElementById('tarif-form'),
        tarifAsal: document.getElementById('tarif-asal'),
        tarifTujuan: document.getElementById('tarif-tujuan'),
        tarifJam: document.getElementById('tarif-jam'),
        tarifResults: document.getElementById('tarif-results'),

        originModal: document.getElementById('origin-modal'),
        btnCloseOrigin: document.getElementById('btn-close-origin'),
    };

    // ==================== HELPERS ====================
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function safePhoto(url) {
        return url && typeof url === 'string' && url.trim() !== '' ? url : FALLBACK_IMG;
    }

    function showToast(message) {
        if (!message) {
            el.mapToast.classList.add('hidden');
            return;
        }
        el.mapToast.textContent = message;
        el.mapToast.classList.remove('hidden');
    }

    async function fetchWisataApi(path, params = {}) {
        const url = new URL(`${CONFIG.wisataProxyApi}${path}`, window.location.origin);
        Object.entries(params).forEach(([k, v]) => {
            if (v !== undefined && v !== null && String(v).trim() !== '') {
                url.searchParams.set(k, v);
            }
        });

        const res = await fetch(url.toString(), {
            headers: { Accept: 'application/json' },
        });
        const json = await res.json();
        if (!res.ok) {
            throw new Error(json.message || 'Gagal mengambil data wisata.');
        }
        return json;
    }

    async function fetchTransportApi(path, params = {}) {
        const url = new URL(`${CONFIG.transportApi}${path}`, window.location.origin);
        Object.entries(params).forEach(([k, v]) => {
            if (v !== undefined && v !== null && String(v).trim() !== '') {
                url.searchParams.set(k, v);
            }
        });

        const res = await fetch(url.toString(), {
            method: 'GET',
            headers: { Accept: 'application/json' },
        });
        const json = await res.json();
        if (!res.ok) {
            throw new Error(json.message || 'Gagal mengambil data transportasi.');
        }
        return json;
    }

    function cleanCityName(raw) {
        return String(raw || '')
            .replace(/^Kabupaten\s+/i, '')
            .replace(/^Kota\s+/i, '')
            .trim();
    }

    // ==================== NAVIGATION ====================
    function goToScreen(name) {
        state.screen = name;

        el.screenLanding.classList.toggle('is-active', name === 'landing');
        el.screenMap.classList.toggle('is-active', name === 'map');
        el.screenDetail.classList.toggle('is-active', name === 'detail');

        el.screenLanding.setAttribute('aria-hidden', String(name !== 'landing'));
        el.screenMap.setAttribute('aria-hidden', String(name !== 'map'));
        el.screenDetail.setAttribute('aria-hidden', String(name !== 'detail'));

        if (name === 'map' && map) {
            setTimeout(() => map.invalidateSize(), 260);
        }

        if (name === 'detail') {
            renderPopularGrid();
            renderSelectedBanner();
            loadRekomendasiTransport();
        }
    }

    // ==================== MAP INIT ====================
    function initMap() {
        map = L.map('map-container', {
            zoomControl: false,
            center: [3.5897, 98.6738], // Pusat Kota Medan
            zoom: 12,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap',
        }).addTo(map);

        markerLayer = L.layerGroup().addTo(map);
    }

    function renderMarkers() {
        markerLayer.clearLayers();
        const bounds = [];

        state.destinasiList.forEach((item) => {
            const lat = parseFloat(item.koordinat?.garis_lintang);
            const lng = parseFloat(item.koordinat?.garis_bujur);
            if (!Number.isFinite(lat) || !Number.isFinite(lng) || (lat === 0 && lng === 0)) {
                return;
            }

            const isSelected = state.selectedWisata && state.selectedWisata.id === item.id;
            const icon = L.divIcon({
                className: 'custom-pin-wrap',
                html: `
                    <div class="pin-node ${isSelected ? 'is-selected' : ''}">
                        <div class="pin-label">${escapeHtml(item.nama)}</div>
                        <div class="pin-dot"></div>
                    </div>
                `,
                iconSize: [0, 0],
                iconAnchor: [0, 0],
            });

            const marker = L.marker([lat, lng], { icon }).addTo(markerLayer);
            marker.on('click', () => selectWisata(item, true));
            bounds.push([lat, lng]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [70, 70], maxZoom: 13 });
        }
    }

    // ==================== WISATA DATA ====================
    async function loadKategori() {
        try {
            const res = await fetchWisataApi('/kategori');
            const items = Array.isArray(res.data) ? res.data : [];

            const chipsHtml = [
                `<button type="button" class="chip ${state.kategori === '' ? 'is-active' : ''}" data-kategori="">Semua</button>`,
            ];

            items.forEach((cat) => {
                const label = cat.nama || cat.kategori || cat.nama_kategori || '';
                const value = cat.slug || cat.slug_kategori || label;
                if (!label) return;
                chipsHtml.push(
                    `<button type="button" class="chip" data-kategori="${escapeHtml(value)}">${escapeHtml(label)}</button>`
                );
            });

            el.categoryChips.innerHTML = chipsHtml.join('');
        } catch (err) {
            console.warn('Gagal memuat kategori:', err.message);
        }
    }

    async function loadKabupatenKota() {
        try {
            const res = await fetchWisataApi('/kabupaten-kota');
            const items = Array.isArray(res.data) ? res.data : [];

            const options = ['<option value="" selected>Semua Wilayah Sumut</option>'];
            items.forEach((item) => {
                const display = item.nama_kabupaten_kota || item.nama || item.kota || '';
                const queryVal = cleanCityName(display);
                if (!queryVal) return;
                options.push(
                    `<option value="${escapeHtml(queryVal)}">${escapeHtml(display)}</option>`
                );
            });

            el.kotaSelect.innerHTML = options.join('');
            state.kota = '';
        } catch (err) {
            console.warn('Gagal memuat kabupaten/kota:', err.message);
        }
    }

    function updateMapPaginationToast() {
        if (state.destinasiList.length === 0) {
            el.mapToast.classList.add('hidden');
            return;
        }

        const loaded = state.destinasiList.length;
        const total = state.totalData || loaded;
        const hasMore = state.halaman < state.totalHalaman;

        el.mapToast.innerHTML = hasMore
            ? `Menampilkan ${loaded} dari ${total} destinasi · ` +
              `<button type="button" id="btn-map-load-more" style="background:none;border:none;color:#F57A21;font-weight:700;cursor:pointer;text-decoration:underline;">+50 Lagi</button>`
            : `Menampilkan semua ${loaded} destinasi`;
        el.mapToast.classList.remove('hidden');

        const btnMore = document.getElementById('btn-map-load-more');
        if (btnMore) {
            btnMore.addEventListener('click', () => loadDestinasi(true));
        }
    }

    async function loadDestinasi(append = false) {
        if (!append) {
            state.halaman = 1;
            showToast('Memuat destinasi wisata Sumut…');
        } else {
            state.halaman += 1;
            showToast(`Memuat halaman ${state.halaman}…`);
        }

        try {
            const res = await fetchWisataApi('/destinasi', {
                kota: state.kota,
                kategori: state.kategori,
                kata_kunci: state.kataKunci,
                halaman: state.halaman,
                batas: 50, // Maksimal yang diizinkan API JejakSumut
            });

            const list = Array.isArray(res.data) ? res.data : [];
            const paginasi = res.paginasi || {};

            state.totalHalaman = Number(paginasi.total_halaman) || 1;
            state.totalData = Number(paginasi.total_data) || list.length;
            state.destinasiList = append ? state.destinasiList.concat(list) : list;

            if (state.destinasiList.length === 0) {
                state.selectedWisata = null;
                el.previewCard.classList.add('hidden');
                markerLayer.clearLayers();
                showToast('Tidak ada destinasi yang cocok dengan filter.');
                return;
            }

            updateMapPaginationToast();

            if (!append) {
                selectWisata(state.destinasiList[0], false);
            }
            renderMarkers();

            if (state.screen === 'detail') {
                renderPopularGrid();
            }
        } catch (err) {
            showToast(err.message);
        }
    }

    function renderPopularGrid() {
        el.popularKotaLabel.textContent = state.kota || 'Semua Wilayah Sumut';

        if (state.destinasiList.length === 0) {
            el.popularGrid.innerHTML = '<div class="empty-box">Belum ada data destinasi.</div>';
            if (el.btnLoadMorePopular) el.btnLoadMorePopular.classList.add('hidden');
            return;
        }

        el.popularGrid.innerHTML = state.destinasiList
            .map((item) => {
                const active = state.selectedWisata && state.selectedWisata.id === item.id;
                const subtitle = `${cleanCityName(item.nama_kabupaten_kota || item.kota || 'Sumut')} · ${item.sub_kategori || item.kategori || 'Wisata'}`;
                return `
                    <button type="button" class="popular-card ${active ? 'is-selected' : ''}" data-id="${item.id}">
                        <img class="popular-img" src="${escapeHtml(safePhoto(item.foto_utama))}" alt="${escapeHtml(item.nama)}" onerror="this.src='${FALLBACK_IMG}'">
                        <span class="popular-name">${escapeHtml(item.nama)}</span>
                        <span class="popular-sub">${escapeHtml(subtitle)}</span>
                    </button>
                `;
            })
            .join('');

        el.popularGrid.querySelectorAll('.popular-card').forEach((btn) => {
            btn.addEventListener('click', () => {
                const found = state.destinasiList.find((d) => String(d.id) === btn.dataset.id);
                if (found) {
                    state.selectedWisata = found;
                    renderPopularGrid();
                    renderSelectedBanner();
                    loadRekomendasiTransport();
                }
            });
        });

        if (el.btnLoadMorePopular) {
            const hasMore = state.halaman < state.totalHalaman;
            el.btnLoadMorePopular.classList.toggle('hidden', !hasMore);
            el.btnLoadMorePopular.textContent = `Muat Lebih Banyak (${state.destinasiList.length} / ${state.totalData})`;
        }
    }

    function selectWisata(item, focusMap = false) {
        state.selectedWisata = item;

        el.previewImg.src = safePhoto(item.foto_utama);
        el.previewImg.onerror = () => {
            el.previewImg.src = FALLBACK_IMG;
        };
        el.previewTitle.textContent = item.nama || 'Destinasi Wisata';
        el.previewAddress.textContent = item.nama_kabupaten_kota || item.kota || 'Sumatera Utara';

        const rating = Number(item.rata_rating) > 0 ? `★ ${Number(item.rata_rating).toFixed(1)}` : '★ Baru';
        const kategori = item.kategori || 'Wisata';
        const tiket = item.label_harga_tiket || 'Cek tiket';
        el.previewMeta.textContent = `${rating}  ·  ${kategori}  ·  ${tiket}`;

        el.previewCard.classList.remove('hidden');

        if (focusMap) {
            renderMarkers();
            const lat = parseFloat(item.koordinat?.garis_lintang);
            const lng = parseFloat(item.koordinat?.garis_bujur);
            if (Number.isFinite(lat) && Number.isFinite(lng)) {
                map.panTo([lat, lng]);
            }
        }
    }

    // ==================== SCREEN 3: DESTINASI & TRANSPORT ====================
    function renderSelectedBanner() {
        const w = state.selectedWisata;
        if (!w) {
            el.selectedBanner.classList.add('hidden');
            return;
        }

        el.selectedBanner.classList.remove('hidden');
        el.bannerImg.src = safePhoto(w.foto_utama);
        el.bannerImg.onerror = () => {
            el.bannerImg.src = FALLBACK_IMG;
        };
        el.bannerCategory.textContent = w.kategori || 'Wisata Sumut';
        el.bannerTitle.textContent = w.nama || '-';
        el.bannerSub.textContent = `${w.nama_kabupaten_kota || w.kota || 'Sumatera Utara'} · ${w.jam_operasional || 'Buka setiap hari'}`;
        el.bannerTicket.textContent = `Tiket: ${w.label_harga_tiket || 'Gratis'}`;

        if (w.url_detail) {
            el.bannerLink.href = w.url_detail;
            el.bannerLink.classList.remove('hidden');
        } else {
            el.bannerLink.classList.add('hidden');
        }
    }

    function renderPopularGrid() {
        el.popularKotaLabel.textContent = state.kota || 'Sumatera Utara';
        const topItems = state.destinasiList.slice(0, 8);

        if (topItems.length === 0) {
            el.popularGrid.innerHTML = '<div class="empty-box">Belum ada data destinasi.</div>';
            return;
        }

        el.popularGrid.innerHTML = topItems
            .map((item) => {
                const active = state.selectedWisata && state.selectedWisata.id === item.id;
                const subtitle = `${cleanCityName(item.nama_kabupaten_kota || item.kota || 'Sumut')} · ${item.sub_kategori || item.kategori || 'Wisata'}`;
                return `
                    <button type="button" class="popular-card ${active ? 'is-selected' : ''}" data-id="${item.id}">
                        <img class="popular-img" src="${escapeHtml(safePhoto(item.foto_utama))}" alt="${escapeHtml(item.nama)}" onerror="this.src='${FALLBACK_IMG}'">
                        <span class="popular-name">${escapeHtml(item.nama)}</span>
                        <span class="popular-sub">${escapeHtml(subtitle)}</span>
                    </button>
                `;
            })
            .join('');

        el.popularGrid.querySelectorAll('.popular-card').forEach((btn) => {
            btn.addEventListener('click', () => {
                const found = state.destinasiList.find((d) => String(d.id) === btn.dataset.id);
                if (found) {
                    state.selectedWisata = found;
                    renderPopularGrid();
                    renderSelectedBanner();
                    loadRekomendasiTransport();
                }
            });
        });
    }

    async function loadRekomendasiTransport() {
        const w = state.selectedWisata;
        if (!w || !w.koordinat) {
            el.transportSummary.classList.add('hidden');
            el.transportSegments.innerHTML =
                '<div class="empty-box">Pilih destinasi wisata terlebih dahulu.</div>';
            return;
        }

        const wisataLat = parseFloat(w.koordinat.garis_lintang);
        const wisataLng = parseFloat(w.koordinat.garis_bujur);

        if (!Number.isFinite(wisataLat) || !Number.isFinite(wisataLng)) {
            el.transportSummary.classList.add('hidden');
            el.transportSegments.innerHTML =
                '<div class="empty-box">Koordinat destinasi ini belum tersedia untuk perhitungan rute.</div>';
            return;
        }

        el.transportSummary.classList.add('hidden');
        el.transportSegments.innerHTML =
            '<div class="empty-box">Menghitung rute & estimasi harga transportasi…</div>';

        try {
            const res = await fetchTransportApi('/transport/rekomendasi-wisata', {
                user_lat: state.origin.lat,
                user_lng: state.origin.lng,
                wisata_lat: wisataLat,
                wisata_lng: wisataLng,
                nama_wisata: w.nama,
                trip_type: state.tripType,
            });

            renderRekomendasiResult(res.data);
        } catch (err) {
            el.transportSegments.innerHTML = `<div class="empty-box">${escapeHtml(err.message)}</div>`;
        }
    }

    function renderSegmentCards(segments, groupLabel) {
        if (!Array.isArray(segments) || segments.length === 0) return '';

        const cards = segments.map((seg) => {
            const berangkat = seg.titik_berangkat?.nama_lokasi || '';
            const tiba = seg.titik_tiba?.nama_lokasi || '';
            const routeTitle =
                berangkat && tiba ? `${berangkat} → ${tiba}` : seg.deskripsi || `Segmen ${seg.segmen}`;

            const opsi = Array.isArray(seg.opsi_armada) ? seg.opsi_armada : [];
            if (opsi.length > 0) {
                return opsi
                    .map((armada) => {
                        const jarakInfo = seg.jarak_km ? ` · ${seg.jarak_km} km` : '';
                        return `
                            <div class="ticket-card">
                                <div class="ticket-left">
                                    <span class="ticket-badge">${escapeHtml(armada.jenis_transport || seg.jenis_segmen)}</span>
                                    <div class="ticket-route">${escapeHtml(routeTitle)}</div>
                                    <div class="ticket-sub">${escapeHtml(armada.penyedia)} · ${escapeHtml(armada.jam_berlaku || '-')}${escapeHtml(jarakInfo)}</div>
                                </div>
                                <div class="ticket-right">
                                    <div class="ticket-price">${escapeHtml(armada.estimasi_tarif_format || seg.tarif_termurah_format || '-')}</div>
                                    <div class="ticket-note">mulai dari</div>
                                </div>
                            </div>
                        `;
                    })
                    .join('');
            }

            return `
                <div class="ticket-card">
                    <div class="ticket-left">
                        <span class="ticket-badge">${escapeHtml(seg.jenis_segmen || 'Transportasi')}</span>
                        <div class="ticket-route">${escapeHtml(routeTitle)}</div>
                        <div class="ticket-sub">${escapeHtml(seg.deskripsi || '')}</div>
                    </div>
                    <div class="ticket-right">
                        <div class="ticket-price">${escapeHtml(seg.tarif_termurah_format || '-')}</div>
                        <div class="ticket-note">estimasi</div>
                    </div>
                </div>
            `;
        });

        return `<div class="group-heading">${escapeHtml(groupLabel)}</div>${cards.join('')}`;
    }

    function renderRekomendasiResult(data) {
        if (!data) {
            el.transportSegments.innerHTML =
                '<div class="empty-box">Data rekomendasi transportasi tidak tersedia.</div>';
            return;
        }

        const meta = data.metadata || {};
        const biaya = data.ringkasan_biaya || {};
        const rute = data.rute_perjalanan || {};

        const hubTerdekat = meta.stasiun_terdekat_wisata
            ? `Simpul terdekat: ${meta.stasiun_terdekat_wisata.nama_lokasi} (${meta.stasiun_terdekat_wisata.jarak_ke_objek_wisata_km} km)`
            : '';

        el.transportSummary.innerHTML = `
            <div>
                <div class="summary-label">Total Estimasi (${escapeHtml(meta.tipe_perjalanan || state.tripType)})</div>
                <div class="summary-total">${escapeHtml(biaya.total_biaya_format || '-')}</div>
                <div class="summary-meta">${escapeHtml(hubTerdekat)}</div>
            </div>
            <div class="summary-pill">
                ${meta.user_di_luar_sumut ? 'Rute Luar Sumut (Udara + Darat)' : 'Rute Dalam Sumut (Darat)'}
            </div>
        `;
        el.transportSummary.classList.remove('hidden');

        const htmlPergi = renderSegmentCards(rute.pergi, 'Rute Keberangkatan');
        const htmlPulang =
            state.tripType === 'roundtrip'
                ? renderSegmentCards(rute.pulang, 'Rute Kepulangan')
                : '';

        el.transportSegments.innerHTML =
            htmlPergi + htmlPulang || '<div class="empty-box">Belum ada jadwal rute untuk koordinat ini.</div>';
    }

    // ==================== KALKULATOR TARIF ANTAR SIMPUL ====================
    async function loadLokasiSimpul() {
        try {
            const res = await fetchTransportApi('/transport/lokasi');
            const items = Array.isArray(res.data) ? res.data : [];
            state.lokasiSimpul = items;

            const opts = items
                .map((l) => `<option value="${escapeHtml(l.id)}">${escapeHtml(l.nama_lokasi)}</option>`)
                .join('');

            el.tarifAsal.innerHTML = opts;
            el.tarifTujuan.innerHTML = opts;
            if (items.length > 1) {
                el.tarifTujuan.selectedIndex = 1;
            }
        } catch (err) {
            el.tarifAsal.innerHTML = '<option value="">Gagal memuat simpul</option>';
            el.tarifTujuan.innerHTML = '<option value="">Gagal memuat simpul</option>';
        }
    }

    async function handleCekTarif(e) {
        e.preventDefault();
        const idAsal = el.tarifAsal.value;
        const idTujuan = el.tarifTujuan.value;
        const jam = el.tarifJam.value;

        if (!idAsal || !idTujuan) return;
        if (idAsal === idTujuan) {
            el.tarifResults.innerHTML =
                '<div class="empty-box">Simpul asal dan tujuan harus berbeda.</div>';
            return;
        }

        el.tarifResults.innerHTML = '<div class="empty-box">Mencari jadwal & tarif…</div>';

        try {
            const res = await fetchTransportApi('/transport/tarif', {
                id_asal: idAsal,
                id_tujuan: idTujuan,
                jam,
            });

            const data = res.data || {};
            const rute = data.rute || {};
            const pilihan = Array.isArray(data.pilihan_transportasi) ? data.pilihan_transportasi : [];

            if (pilihan.length === 0) {
                el.tarifResults.innerHTML =
                    '<div class="empty-box">Tidak ada jadwal aktif pada rute dan jam tersebut.</div>';
                return;
            }

            const routeLabel = `${rute.asal?.nama_lokasi || ''} → ${rute.tujuan?.nama_lokasi || ''}`;
            el.tarifResults.innerHTML = pilihan
                .map(
                    (item) => `
                    <div class="ticket-card">
                        <div class="ticket-left">
                            <span class="ticket-badge">${escapeHtml(item.jenis_transport)}</span>
                            <div class="ticket-route">${escapeHtml(item.penyedia)}</div>
                            <div class="ticket-sub">${escapeHtml(routeLabel)} · ${escapeHtml(rute.jarak_km)} km · ${escapeHtml(item.jam_berlaku)}</div>
                        </div>
                        <div class="ticket-right">
                            <div class="ticket-price">${escapeHtml(item.estimasi_tarif_format)}</div>
                            <div class="ticket-note">mulai dari</div>
                        </div>
                    </div>
                `
                )
                .join('');
        } catch (err) {
            el.tarifResults.innerHTML = `<div class="empty-box">${escapeHtml(err.message)}</div>`;
        }
    }

    // ==================== ORIGIN / GPS HANDLING ====================
    function setOriginPreset(key) {
        if (key === 'gps') {
            detectGpsOrigin();
            return;
        }

        const preset = ORIGIN_PRESETS[key];
        if (!preset) return;

        state.originKey = key;
        state.origin = { ...preset };
        el.landingOriginLabel.textContent = preset.label;
        el.originSelectDetail.value = key;
        el.originModal.classList.add('hidden');

        if (state.screen === 'detail') {
            loadRekomendasiTransport();
        }
    }

    function detectGpsOrigin() {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung geolokasi GPS.');
            return;
        }

        el.landingOriginLabel.textContent = 'Mendeteksi GPS…';
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                state.originKey = 'gps';
                state.origin = {
                    label: `GPS (${pos.coords.latitude.toFixed(2)}, ${pos.coords.longitude.toFixed(2)})`,
                    lat: pos.coords.latitude,
                    lng: pos.coords.longitude,
                };
                el.landingOriginLabel.textContent = state.origin.label;
                el.originSelectDetail.value = 'gps';
                el.originModal.classList.add('hidden');

                if (state.screen === 'detail') {
                    loadRekomendasiTransport();
                }
            },
            () => {
                alert('Izin GPS ditolak atau gagal. Menggunakan lokasi Medan sebagai default.');
                setOriginPreset('medan');
            },
            { timeout: 6000 }
        );
    }

    // ==================== EVENT LISTENERS ====================
    function bindEvents() {
        el.btnStart.addEventListener('click', () => goToScreen('map'));
        el.btnBackLanding.addEventListener('click', () => goToScreen('landing'));
        el.btnToDetail.addEventListener('click', () => goToScreen('detail'));
        el.btnBackMap.addEventListener('click', () => goToScreen('map'));

        el.btnOpenOrigin.addEventListener('click', () => el.originModal.classList.remove('hidden'));
        el.btnCloseOrigin.addEventListener('click', () => el.originModal.classList.add('hidden'));
        el.originModal.addEventListener('click', (e) => {
            if (e.target === el.originModal) el.originModal.classList.add('hidden');
        });

        document.querySelectorAll('.preset-btn').forEach((btn) => {
            btn.addEventListener('click', () => setOriginPreset(btn.dataset.origin));
        });

        el.kotaSelect.addEventListener('change', () => {
            state.kota = el.kotaSelect.value;
            loadDestinasi();
        });

        el.searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                state.kataKunci = el.searchInput.value.trim();
                loadDestinasi();
            }, 350);
        });

        el.categoryChips.addEventListener('click', (e) => {
            const btn = e.target.closest('.chip');
            if (!btn) return;
            el.categoryChips.querySelectorAll('.chip').forEach((c) => c.classList.remove('is-active'));
            btn.classList.add('is-active');
            state.kategori = btn.dataset.kategori || '';
            loadDestinasi();
        });

        el.originSelectDetail.addEventListener('change', () => {
            setOriginPreset(el.originSelectDetail.value);
        });

        if (el.btnLoadMorePopular) {
            el.btnLoadMorePopular.addEventListener('click', () => loadDestinasi(true));
        }

        el.btnUseGps.addEventListener('click', detectGpsOrigin);

        [el.btnTripRoundtrip, el.btnTripOneway].forEach((btn) => {
            btn.addEventListener('click', () => {
                state.tripType = btn.dataset.trip;
                el.btnTripRoundtrip.classList.toggle('is-active', state.tripType === 'roundtrip');
                el.btnTripOneway.classList.toggle('is-active', state.tripType === 'oneway');
                loadRekomendasiTransport();
            });
        });

        el.tarifForm.addEventListener('submit', handleCekTarif);
    }

    // ==================== BOOT ====================
    async function boot() {
        initMap();
        bindEvents();

        await Promise.all([loadKategori(), loadKabupatenKota(), loadLokasiSimpul()]);
        await loadDestinasi();
    }

    boot();
})();