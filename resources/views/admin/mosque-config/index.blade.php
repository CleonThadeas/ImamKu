@extends('layouts.app')
@section('title', 'Konfigurasi Lokasi Masjid')

@section('content')
<div class="main-header">
    <div>
        <h2>Konfigurasi Lokasi Masjid</h2>
        <div class="breadcrumb">Atur titik koordinat GPS dan radius toleransi absensi</div>
    </div>
</div>

@if($season)
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display:flex;align-items:center;gap:8px">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Lokasi Masjid — {{ $season->name }}
        </h3>
        @if($config)
            <span class="badge badge-success">Terkonfigurasi</span>
        @else
            <span class="badge badge-warning">Belum diatur</span>
        @endif
    </div>

    <form method="POST" action="{{ route('admin.mosque-config.store') }}" style="padding:20px">
        @csrf
        <input type="hidden" name="season_id" value="{{ $season->id }}">

        <div class="form-group">
            <label class="form-label">Nama Masjid</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $config->name ?? '') }}" placeholder="Masjid Al-Ikhlas" required>
        </div>

        <!-- Coordinate Inputs + Geolocation Feature -->
        <div style="background:var(--clr-surface-light); border:1px solid var(--clr-border); border-radius:8px; padding:16px; margin-bottom:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
                <h4 style="margin:0; font-size:0.95rem; display:flex; align-items:center; gap:8px">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    Koordinat Lokasi
                </h4>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn btn-secondary btn-sm" id="btnToggleMap" style="background:var(--clr-bg)">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg> Tampilkan Peta
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="btnGetLocation">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px"><circle cx="12" cy="12" r="10"/><path d="M12 2v4M12 18v4M2 12h4M18 12h4"/></svg>
                        Lacak Lokasi Saya
                    </button>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div class="form-group" style="margin:0">
                    <label class="form-label">Latitude (Manual/Otomatis)</label>
                    <input type="number" name="latitude" id="inputLat" class="form-input" step="0.00000001" value="{{ old('latitude', $config->latitude ?? '') }}" placeholder="-6.12345678" required>
                </div>
                <div class="form-group" style="margin:0">
                    <label class="form-label">Longitude (Manual/Otomatis)</label>
                    <input type="number" name="longitude" id="inputLng" class="form-input" step="0.00000001" value="{{ old('longitude', $config->longitude ?? '') }}" placeholder="106.12345678" required>
                </div>
            </div>

            <!-- Interactive Map Wrapper (Hidden by default) -->
            <div id="mapContainer" style="display:none;">
                <div id="mosqueMap" style="height: 350px; border-radius: 8px; border: 1px solid var(--clr-border); z-index: 1;"></div>
                <p style="font-size:0.75rem;color:var(--clr-text-muted);margin-top:8px;display:flex;justify-content:space-between">
                    <span><strong>Info Peta:</strong> Klik pada peta atau geser marker untuk mengubah koordinat di atas.</span>
                    <span id="geoStatus"></span>
                </p>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div class="form-group" style="margin:0">
                <label class="form-label">Radius Toleransi (meter)</label>
                <input type="number" name="radius_meters" class="form-input" min="{{ $bounds['min'] ?? 50 }}" max="{{ $bounds['max'] ?? 500 }}" value="{{ old('radius_meters', $config->radius_meters ?? ($bounds['default'] ?? 100)) }}" required>
                <small style="color:var(--clr-text-muted)">Min: {{ $bounds['min'] ?? 50 }}m | Max: {{ $bounds['max'] ?? 500 }}m</small>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
            <div class="form-group" style="margin:0">
                <label class="form-label">Jendela Terbuka (menit sebelum sholat)</label>
                <input type="number" name="attendance_window_minutes" class="form-input" min="10" max="120" value="{{ old('attendance_window_minutes', $config->attendance_window_minutes ?? 30) }}" required>
                <small style="color:var(--clr-text-muted)">Absen dibuka X menit sebelum waktu sholat</small>
            </div>
            
            <div class="form-group" style="margin:0">
                <label class="form-label">Jendela Tertutup (menit setelah sholat)</label>
                <input type="number" name="attendance_window_after_minutes" class="form-input" min="10" max="120" value="{{ old('attendance_window_after_minutes', $config->attendance_window_after_minutes ?? 30) }}" required>
                <small style="color:var(--clr-text-muted)">Absen ditutup X menit setelah waktu sholat</small>
            </div>
        </div>

        <div style="margin-top:20px;display:flex;gap:8px">
            <button type="submit" class="btn btn-primary">Simpan Konfigurasi</button>
        </div>
    </form>
</div>

@if($config)
<div class="card" style="margin-top:20px">
    <div class="card-header">
        <h3 class="card-title">Ringkasan Konfigurasi</h3>
    </div>
    <div style="padding:20px">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon green"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
                <div>
                    <div class="stat-value" style="font-size:0.9rem">{{ $config->latitude }}, {{ $config->longitude }}</div>
                    <div class="stat-label">Koordinat</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon gold"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg></div>
                <div>
                    <div class="stat-value">{{ $config->radius_meters }}m</div>
                    <div class="stat-label">Radius Toleransi</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
                <div>
                    <div class="stat-value" style="font-size:1.1rem">-{{ $config->attendance_window_minutes }} / +{{ $config->attendance_window_after_minutes ?? 30 }} mnt</div>
                    <div class="stat-label">Batas Absensi</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@else
<div class="card">
    <div class="empty-state">
        <div class="empty-icon" style="font-size:2rem;opacity:0.3">—</div>
        <p>Buat Season Ramadan terlebih dahulu.</p>
    </div>
</div>
</div>
@endif

@push('scripts')
<!-- Leaflet CSS & JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const latInput = document.getElementById('inputLat');
        const lngInput = document.getElementById('inputLng');
        const btnToggleMap = document.getElementById('btnToggleMap');
        const btnGetLocation = document.getElementById('btnGetLocation');
        const mapContainer = document.getElementById('mapContainer');
        const geoStatus = document.getElementById('geoStatus');
        
        // Default location if no config yet (Jakarta center)
        let initialLat = latInput.value ? parseFloat(latInput.value) : -6.2088;
        let initialLng = lngInput.value ? parseFloat(lngInput.value) : 106.8456;

        let map = null;
        let marker = null;
        let mapIsVisible = false;

        function initMap() {
            if (map !== null) return; // already init

            map = L.map('mosqueMap').setView([initialLat, initialLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

            // Update inputs on marker drag
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateInputs(position.lat, position.lng);
                map.panTo(position);
            });

            // Update marker and inputs on map click
            map.on('click', function(e) {
                const position = e.latlng;
                marker.setLatLng(position);
                updateInputs(position.lat, position.lng);
            });
        }

        // Toggle Map feature
        btnToggleMap.addEventListener('click', function() {
            mapIsVisible = !mapIsVisible;
            if (mapIsVisible) {
                mapContainer.style.display = 'block';
                btnToggleMap.innerHTML = 'Tutup Peta';
                initMap();
                map.invalidateSize(); // Fix tile rendering issue when revealing hidden map
                
                // Keep marker synced with inputs when opening map
                updateMapFromInputs();
            } else {
                mapContainer.style.display = 'none';
                btnToggleMap.innerHTML = 'Tampilkan Peta';
            }
        });

        // Get Location feature
        btnGetLocation.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert("Browser Anda tidak mendukung fitur lokasi GPS.");
                return;
            }

            const originBtnText = btnGetLocation.innerHTML;
            btnGetLocation.innerHTML = 'Sedang melacak...';
            btnGetLocation.disabled = true;
            if(geoStatus) geoStatus.innerHTML = '<span style="color:var(--clr-warning)">Meminta izin akses lokasi...</span>';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Success
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    updateInputs(lat, lng);
                    
                    if (mapIsVisible && map !== null) {
                        const newPos = new L.LatLng(lat, lng);
                        marker.setLatLng(newPos);
                        map.panTo(newPos);
                    } else {
                        // If map is hidden, we just update initial coords for when it opens
                        initialLat = lat;
                        initialLng = lng;
                    }

                    btnGetLocation.innerHTML = originBtnText;
                    btnGetLocation.disabled = false;
                    if(geoStatus) geoStatus.innerHTML = '<span style="color:var(--clr-success)">Lokasi ditemukan!</span>';
                    
                    setTimeout(() => { if(geoStatus) geoStatus.innerHTML = ''; }, 3000);
                },
                function(error) {
                    // Error
                    btnGetLocation.innerHTML = originBtnText;
                    btnGetLocation.disabled = false;
                    
                    let errorMsg = "Gagal mendapatkan lokasi.";
                    if (error.code === error.PERMISSION_DENIED) errorMsg = "Izin lokasi ditolak oleh browser Anda. Mohon izinkan akses.";
                    else if (error.code === error.POSITION_UNAVAILABLE) errorMsg = "Informasi lokasi tidak tersedia pada perangkat Anda.";
                    else if (error.code === error.TIMEOUT) errorMsg = "Request lokasi timeout / terlalu lama.";
                    
                    alert(errorMsg);
                    if(geoStatus) geoStatus.innerHTML = `<span style="color:var(--clr-danger)">Gagal: ${errorMsg}</span>`;
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });

        // Manual Input sync
        latInput.addEventListener('input', updateMapFromInputs);
        lngInput.addEventListener('input', updateMapFromInputs);

        function updateInputs(lat, lng) {
            latInput.value = lat.toFixed(8);
            lngInput.value = lng.toFixed(8);
        }

        function updateMapFromInputs() {
            if (!mapIsVisible || map === null) return;
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);
            if (!isNaN(lat) && !isNaN(lng)) {
                const newPos = new L.LatLng(lat, lng);
                marker.setLatLng(newPos);
                map.panTo(newPos);
            }
        }
    });
</script>
@endpush
@endsection
