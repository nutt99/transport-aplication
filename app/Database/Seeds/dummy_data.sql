-- =============================================================================
-- DUMMY DATA FOR TRANSPORT APPLICATION (PostgreSQL)
-- =============================================================================

-- 1. Reset sequence & bersihkan data
TRUNCATE TABLE jadwal, penyedia, kota, transport RESTART IDENTITY CASCADE;

-- 2. Data Transport
INSERT INTO transport (id, jenis, created_at, updated_at) VALUES
(1, 'Bus', NOW(), NOW()),
(2, 'Kereta Api', NOW(), NOW()),
(3, 'Pesawat', NOW(), NOW()),
(4, 'Kapal Laut', NOW(), NOW()),
(5, 'Travel / Shuttle', NOW(), NOW());
SELECT setval('transport_id_seq', (SELECT MAX(id) FROM transport));

-- 3. Data Kota
INSERT INTO kota (id, nama_kota, latitude, longitude, created_at, updated_at) VALUES
(1, 'Jakarta', -6.20876340, 106.84559900, NOW(), NOW()),
(2, 'Bandung', -6.91746390, 107.61912280, NOW(), NOW()),
(3, 'Semarang', -6.96666700, 110.41666400, NOW(), NOW()),
(4, 'Yogyakarta', -7.79557980, 110.36948960, NOW(), NOW()),
(5, 'Surakarta', -7.57548870, 110.82432630, NOW(), NOW()),
(6, 'Surabaya', -7.25747190, 112.75208830, NOW(), NOW()),
(7, 'Malang', -7.96662040, 112.63263210, NOW(), NOW()),
(8, 'Denpasar', -8.67045820, 115.21262930, NOW(), NOW()),
(9, 'Medan', 3.59519560, 98.67222270, NOW(), NOW()),
(10, 'Makassar', -5.14766510, 119.43273140, NOW(), NOW());
SELECT setval('kota_id_seq', (SELECT MAX(id) FROM kota));

-- 4. Data Penyedia
INSERT INTO penyedia (id, id_transport, nama_penyedia, latitude, longitude, created_at, updated_at) VALUES
(1, 1, 'PO Sinar Jaya', -6.22501400, 106.90044700, NOW(), NOW()),
(2, 1, 'PO Rosalia Indah', -7.56841200, 110.85210400, NOW(), NOW()),
(3, 1, 'PO Primajasa', -6.23049100, 106.88210300, NOW(), NOW()),
(4, 1, 'DAMRI', -6.21528900, 106.87023100, NOW(), NOW()),
(5, 1, 'PO Gunung Harta', -8.65012300, 115.21045200, NOW(), NOW()),
(6, 2, 'PT Kereta Api Indonesia (Persero)', -6.91474400, 107.60981000, NOW(), NOW()),
(7, 2, 'KCIC Whoosh', -6.24431200, 106.87789400, NOW(), NOW()),
(8, 2, 'KAI Commuter', -6.17539200, 106.82715300, NOW(), NOW()),
(9, 3, 'Garuda Indonesia', -6.12555600, 106.65583300, NOW(), NOW()),
(10, 3, 'Citilink', -6.12720000, 106.65340000, NOW(), NOW()),
(11, 3, 'Lion Air', -6.12610000, 106.65420000, NOW(), NOW()),
(12, 3, 'Batik Air', -6.12650000, 106.65480000, NOW(), NOW()),
(13, 3, 'Super Air Jet', -6.12800000, 106.65210000, NOW(), NOW()),
(14, 4, 'PT Pelayaran Nasional Indonesia (PELNI)', -6.10520000, 106.88360000, NOW(), NOW()),
(15, 4, 'PT ASDP Indonesia Ferry', -5.92980000, 105.99610000, NOW(), NOW()),
(16, 5, 'Baraya Travel', -6.21150000, 106.84380000, NOW(), NOW()),
(17, 5, 'DayTrans', -6.22010000, 106.82050000, NOW(), NOW()),
(18, 5, 'Cititrans', -6.22340000, 106.81120000, NOW(), NOW());
SELECT setval('penyedia_id_seq', (SELECT MAX(id) FROM penyedia));

-- 5. Data Jadwal
INSERT INTO jadwal (id, id_penyedia, id_kota_asal, id_kota_tujuan, start_hours, ended_hours, tarif_per_km, created_at, updated_at) VALUES
-- KAI
(1, 6, 1, 2, '06:30:00', '09:15:00', 750.00, NOW(), NOW()),
(2, 6, 2, 1, '10:00:00', '12:45:00', 750.00, NOW(), NOW()),
(3, 6, 1, 4, '08:00:00', '15:30:00', 650.00, NOW(), NOW()),
(4, 6, 4, 1, '16:30:00', '23:55:00', 650.00, NOW(), NOW()),
(5, 6, 1, 6, '09:00:00', '19:30:00', 700.00, NOW(), NOW()),
(6, 6, 6, 1, '20:00:00', '06:30:00', 700.00, NOW(), NOW()),
(7, 6, 2, 6, '07:00:00', '18:30:00', 680.00, NOW(), NOW()),
-- Whoosh
(8, 7, 1, 2, '07:00:00', '07:45:00', 1750.00, NOW(), NOW()),
(9, 7, 2, 1, '08:30:00', '09:15:00', 1750.00, NOW(), NOW()),
(10, 7, 1, 2, '17:00:00', '17:45:00', 1750.00, NOW(), NOW()),
-- Bus Sinar Jaya
(11, 1, 1, 3, '07:30:00', '14:30:00', 350.00, NOW(), NOW()),
(12, 1, 3, 1, '19:00:00', '02:00:00', 350.00, NOW(), NOW()),
-- Bus Rosalia Indah
(13, 2, 1, 5, '08:00:00', '16:30:00', 400.00, NOW(), NOW()),
(14, 2, 5, 1, '18:30:00', '03:00:00', 400.00, NOW(), NOW()),
-- DAMRI
(15, 4, 1, 2, '06:00:00', '09:30:00', 380.00, NOW(), NOW()),
-- Bus Gunung Harta
(16, 5, 6, 8, '16:00:00', '05:00:00', 420.00, NOW(), NOW()),
(17, 5, 8, 7, '17:00:00', '06:30:00', 430.00, NOW(), NOW()),
-- Garuda Indonesia
(18, 9, 1, 8, '07:00:00', '09:50:00', 1850.00, NOW(), NOW()),
(19, 9, 8, 1, '11:00:00', '11:50:00', 1850.00, NOW(), NOW()),
(20, 9, 1, 9, '08:30:00', '10:50:00', 1700.00, NOW(), NOW()),
-- Citilink
(21, 10, 1, 6, '06:00:00', '07:30:00', 1200.00, NOW(), NOW()),
(22, 10, 6, 1, '08:30:00', '10:00:00', 1200.00, NOW(), NOW()),
-- Lion Air
(23, 11, 1, 10, '09:00:00', '12:25:00', 1100.00, NOW(), NOW()),
-- Super Air Jet
(24, 13, 1, 4, '10:00:00', '11:15:00', 1150.00, NOW(), NOW()),
-- Travel Baraya
(25, 16, 1, 2, '06:00:00', '09:00:00', 650.00, NOW(), NOW()),
(26, 16, 1, 2, '13:00:00', '16:00:00', 650.00, NOW(), NOW()),
-- Travel DayTrans
(27, 17, 1, 2, '07:30:00', '10:30:00', 680.00, NOW(), NOW()),
(28, 17, 2, 1, '15:00:00', '18:00:00', 680.00, NOW(), NOW()),
-- Travel Cititrans
(29, 18, 1, 2, '08:00:00', '11:00:00', 750.00, NOW(), NOW()),
-- Kapal PELNI
(30, 14, 6, 10, '10:00:00', '06:00:00', 250.00, NOW(), NOW()),
-- Kapal ASDP
(31, 15, 8, 6, '08:00:00', '18:00:00', 280.00, NOW(), NOW());
SELECT setval('jadwal_id_seq', (SELECT MAX(id) FROM jadwal));
