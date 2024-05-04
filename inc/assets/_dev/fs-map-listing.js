import {createMarkers, initBaristaMap} from "./barista-map";

const API_KEY = 'AIzaSyAitFZqjWLqRCzMd8FLqbTjeQnDnVbWwYE';

(g => {
    let h, a, k, p = "The Google Maps JavaScript API", c = "google", l = "importLibrary", q = "__ib__", m = document,
        b = window;
    b = b[c] || (b[c] = {});
    let d = b.maps || (b.maps = {}), r = new Set, e = new URLSearchParams,
        u = () => h || (h = new Promise(async (f, n) => {
            await (a = m.createElement("script"));
            e.set("libraries", [...r] + "");
            for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
            e.set("callback", c + ".maps." + q);
            a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
            d[q] = f;
            a.onerror = () => h = n(Error(p + " could not load."));
            a.nonce = m.querySelector("script[nonce]")?.nonce || "";
            m.head.append(a)
        }));
    d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
})({key: API_KEY, v: "weekly", region: 'AU'});

jQuery(async function ($) {
    console.log('lt map listing ready');
    const {MarkerClusterer} = await eval(`import("https://cdn.skypack.dev/@googlemaps/markerclusterer@2.5.3")`);

    const getBaristaData = (barista_data) => {
        const data = (barista_data ? barista_data : ajax_data.barista_data)
            ?.flatMap(
                ({
                     locations, ...rest
                 }) => locations ? locations.map((item) => ({...rest, ...item})) : rest)
        return (data?.filter(e => !!e.location)?.map(item => {
            const {location: {lat, lng} = {}, ...rest} = item || {}
            return {
                position: {lat, lng},
                ...rest
            }
        }))
    }

    const setDataRadius = (circle, map, place, currentMarker) => {
        circle.setMap(map);
        circle.setCenter(place.geometry.location)
        const bounds = circle.getBounds()
        if (bounds) {
            map.fitBounds(bounds);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(10);
        }
        currentMarker.position = (place.geometry.location);
        currentMarker.setMap(map)
    };

    const showPosition = async (latLng, map, circle, currentMarker) => {
        const {AdvancedMarkerElement} = await google.maps.importLibrary(
            "marker",
        );
        const geocoder = new google.maps.Geocoder();

        geocoder
            .geocode({location: latLng})
            .then((response) => {
                if (response.results[0]) {
                    $('.__lt-input-location >.icon-location').addClass('__found')
                    const place = response.results[0]
                    const address = place.formatted_address
                    $('.__lt-input-location >input[name="your_location"]').val(address)

                    setDataRadius(circle, map, place, currentMarker);
                } else {
                    window.alert("No results found");
                }
            })
            .catch((e) => window.alert("Geocoder failed due to: " + e));
    };

    const showError = error => {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.")
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.")
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.")
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.")
                break;
        }
    };

    const autocompleteAddress = async () => {
        const {Autocomplete} = await google.maps.importLibrary("places")
        const input = document.querySelector('.__lt-input-location >input[name="your_location"]');
        const inputMobile = document.querySelector('.__lt-filter-modal .__lt-input-location >input[name="your_location"]');
        const autocomplete = new Autocomplete(input, {
            fields: ["formatted_address", "geometry", "name"],
            componentRestrictions: {country: 'au'},
            strictBounds: false,
        });
        const autocompleteMobile = new Autocomplete(inputMobile, {
            fields: ["formatted_address", "geometry", "name"],
            componentRestrictions: {country: 'au'},
            strictBounds: false,
        });

        return {autocomplete, autocompleteMobile}
    }
    const calculateDistance = (point1, point2) => {
        const R = 6371;

        const lat1 = point1.lat * Math.PI / 180;
        const lon1 = point1.lng * Math.PI / 180;

        const lat2 = point2.lat * Math.PI / 180;
        const lon2 = point2.lng * Math.PI / 180;

        const dLat = Math.abs(lat2 - lat1);
        const dLon = Math.abs(lon2 - lon1);

        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(lat1) * Math.cos(lat2) *
            Math.sin(dLon / 2) * Math.sin(dLon / 2);

        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        const distance = R * c;

        return distance;
    };

    const filterBaristaByRadius = (latLng, radiusParam) => {
        const $wrap = $(".__listings-wrap"), radiusInput = $wrap.find('input[name="radius"]')
        const radius = radiusParam ? radiusParam : (+radiusInput?.val() || 10)
        const listBarista = getBaristaData($wrap.data('barista-filtered'))
        const baristaFilter = listBarista.filter(item => calculateDistance(latLng, item.position) <= radius)
        const uniqueIds = new Set(baristaFilter.map(item => item.id));
        const count = uniqueIds.size

        $(".__counter-result").text(`(${count} barista${count > 1 ? "s" : ""})`)
        return baristaFilter
    }

    const initMap = async () => {
        const $wrap = $(".__listings-wrap");
        const map = await initBaristaMap()
        if (!map) {
            console.error('Map object not yet initialized')
            return;
        }

        const {AdvancedMarkerElement} = await google.maps.importLibrary(
            "marker",
        );
        const {LatLngBounds, LatLng} = await google.maps.importLibrary("core")
        const currentMarker = new AdvancedMarkerElement({
            map: map,
        });
        const markerCluster = new MarkerClusterer({markers: [], map});
        const bounds = new LatLngBounds();
        const circle = new google.maps.Circle({
            strokeColor: "#5996e4",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#5996e4",
            fillOpacity: 0.35,
            radius: 10000,
            // editable: true,
            // draggable: true,
        });
        const updateMap = async (baristaData) => {
            markerCluster.clearMarkers();
            if (!baristaData.length) return
            const markers = await createMarkers(map, baristaData);
            markerCluster.addMarkers(markers);

            const boundsC = circle.getBounds()
            if (boundsC) {
                map.fitBounds(boundsC);
            } else {
                baristaData?.forEach(({position}) => bounds.extend(new LatLng(position.lat, position.lng)));
                map.fitBounds(bounds);
            }
        };
        await updateMap(getBaristaData());

        const {autocomplete, autocompleteMobile} = await autocompleteAddress()
        autocomplete.addListener("place_changed", async function () {
            const place = autocomplete.getPlace();
            if (!place.geometry || !place.geometry.location) {
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }
            const radius = +$('.__lt-filter-side-inner  .__lt-input-radius input[name="radius"]')?.val()

            currentMarker.setMap(null)
            circle.setMap(null);
            setDataRadius(circle, map, place, currentMarker);
            const latLng = {lat: place.geometry.location.lat(), lng: place.geometry.location.lng()}
            const listBarista = getBaristaData($wrap.data('barista-filtered'))
            await updateMap(listBarista);
        });
        autocompleteMobile.addListener("place_changed", async function () {
            const place = autocompleteMobile.getPlace();
            if (!place.geometry || !place.geometry.location) {
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }
            const radius = +$('.__lt-filter-modal .__lt-input-radius input[name="radius"]')?.val()
            currentMarker.setMap(null)
            circle.setMap(null);
            setDataRadius(circle, map, place, currentMarker);
            const latLng = {lat: place.geometry.location.lat(), lng: place.geometry.location.lng()}
            const listBarista = getBaristaData($wrap.data('barista-filtered'))
            await updateMap(listBarista);
        });

        document.addEventListener('baristaFiltering', async (event) => {
            if (event.detail.data) {
                currentMarker.setMap(null)
                circle.setMap(null);
                circle.setCenter(null);
                $('.__lt-input-location >input[name="your_location"]').val('')
                $('.__lt-input-location >.icon-location').removeClass('__found')
                await updateMap(getBaristaData(event.detail.data));
            }
        });
        $('.__lt-input-location >.icon-location').on('click', async function (e) {
            const parent = $(this).parents('.__lt-filter-group');
            const radius = +$(parent).find('.__lt-input-radius input[name="radius"]')?.val()
            currentMarker.setMap(null)
            circle.setMap(null);
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async (position) => {
                    const latLng = {lat: position.coords.latitude, lng: position.coords.longitude}
                    const listBarista = getBaristaData($wrap.data('barista-filtered'))
                    await updateMap(listBarista);
                    await showPosition(latLng, map, circle, currentMarker);
                }, showError);
            } else {
                alert("Geolocation is not supported by this browser.")
            }
        })
        $('.__lt-input-radius input[name="radius"]').on('change', async function (e) {
            const radius = +$(this)?.val() || 10
            circle.setRadius(radius * 1000)
            const place = await circle.getCenter()
            if (!place) return
            const latLng = {lat: place.lat(), lng: place.lng()}
            const listBarista = getBaristaData($wrap.data('barista-filtered'))
            await updateMap(listBarista);
        })
    };

    initMap();


});
