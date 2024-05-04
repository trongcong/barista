var baristaMarkers = [];

export async function initBaristaMap() {
    const mapEl = document.getElementById("barista-map")
    if (!mapEl) return
    const {Map} = await google.maps.importLibrary("maps");
    const map = new Map(mapEl, {
        zoom: 6,
        mapId: "barista-listing",
    });
    return map;
}

const generateIconAvatar = (avatar, name, data) => {
    const BARISTA_VIEWED_KEY  = 'barista-viewed'
    const className = ["avatar-tag"]
    const post_id = +data?.id
    const baristaViewed = JSON.parse(localStorage.getItem(BARISTA_VIEWED_KEY)) || [];
    if (baristaViewed?.includes(post_id)) {
        className.push('_viewed')
    }
    const avatarTag = document.createElement("div");
    const html = `<img src="${avatar}" alt="${name}"/>`
    avatarTag.className = className.join(' ');
    avatarTag.innerHTML = html
    return avatarTag;
};

async function createInfoWindow(marker, infoWindow, data) {
    marker.addListener("click", ({domEvent, ...rest}) => {
        const {target} = domEvent;
        const content = `<div class="barista-map-info-item">
    <h4><a href="${data.url}">${marker.title}</a></h4>
    <div class="meta">
        <div class="__num-exp">
            Barista:
            <strong>${data.years_of_experience}</strong>
        </div>
        <div class="__num-exp-aus">
            Retail or Hospo:
            <strong>${data.experience_in_australia}</strong>
        </div>
        <div class="__num-volume">
            Volume (solo):
            <strong>${data.volume}</strong>
        </div>
    </div>
</div>`

        infoWindow.close();
        infoWindow.setContent(content);
        infoWindow.open(marker.map, marker);
    });
}

export async function createMarkers(map, baristaData) {
    const {AdvancedMarkerElement} = await google.maps.importLibrary(
        "marker",
    );
    const {InfoWindow} = await google.maps.importLibrary("maps");
    const infoWindow = new InfoWindow({
        content: "",
        disableAutoPan: true,
    });

    baristaMarkers.forEach(marker => marker.setMap(null));
    baristaMarkers.length = 0;

    const newMarkers = [];
    for (const {position, title, avatar, ...rest} of baristaData) {
        const avatarTag = generateIconAvatar(avatar, title, rest);
        const marker = new AdvancedMarkerElement({
            position,
            map,
            title,
            content: avatarTag,
        });
        await createInfoWindow(marker, infoWindow, rest);
        newMarkers.push(marker);
    }
    baristaMarkers.push(...newMarkers);
    return newMarkers
}