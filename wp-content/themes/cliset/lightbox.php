<?php
/**
 * The template for displaying the footer.
 *
 * @package woostify
 */

do_action('woostify_theme_footer');


?>

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9JFM43xHL57gg_aTN_gqePaH9eIx84b8&libraries=places&v=weekly"
    defer>
    </script>
<!-- <div id="result">City, State, Pincode will appear here.</div> -->

<script>
    window.selectedAddress = "";
    window.selectedPlaceData = null;

    jQuery(document).ready(function ($) {

        function updateBackground() {
            var activeSlide = $('.product-images .is-selected');

            if (activeSlide.length) {
                var imgSrc = activeSlide.find('a').attr('href');

                $('.product-images').css({
                    'background-image': 'url(' + imgSrc + ')',
                    'background-size': 'cover',
                    'background-position': 'center'
                });
            }
        }

        function initBackgroundSync() {

            // Remove old event (IMPORTANT)
            $('.product-images-container').off('select.flickity');

            // Add again
            $('.product-images-container').on('select.flickity', function () {
                setTimeout(updateBackground, 100);
            });

            // Run once
            setTimeout(updateBackground, 200);
        }

        // First load
        initBackgroundSync();

        // ✅ Fix for popup open/close (Elementor / PhotoSwipe)
        $(document).on('click', '.dialog-close-button, .pswp__button--close', function () {
            setTimeout(function () {
                initBackgroundSync();
            }, 300);
        });

        // ✅ EXTRA STRONG FIX (handles re-render)
        const observer = new MutationObserver(function () {
            initBackgroundSync();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

    });

    document.addEventListener("DOMContentLoaded", function () {

        const openBtn = document.getElementById("openLocationPopup");
        const closeBtn = document.getElementById("closeLocationPopup");
        const modal = document.getElementById("locationModal");

        if (openBtn && modal) {
            openBtn.addEventListener("click", function () {
                modal.style.display = "flex";
            });
        }

        if (closeBtn && modal) {
            closeBtn.addEventListener("click", function () {
                modal.style.display = "none";
            });
        }

        // Close when clicking outside popup
        if (modal) {
            modal.addEventListener("click", function (e) {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
        }

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const topbar = document.querySelector(".topbar-item.topbar-right");
        const saveBtn = document.querySelector(".save-btn");
        const autocomplete = document.getElementById("locationSearch");

        let selectedAddress = "";
        let selectedPlaceData = null;

        // Load saved address on page load

        const savedAddress = "<?php
        $address = '';
        if (is_user_logged_in()) {
            $address = get_user_meta(get_current_user_id(), '_user_location', true);
        } elseif (isset($_COOKIE['guest_location'])) {
            $address = sanitize_text_field($_COOKIE['guest_location']);
        }
        // echo esc_attr($address);
        
        $location = is_user_logged_in()
            ? get_user_meta(get_current_user_id(), '_user_location', true)
            : (isset($_COOKIE['guest_location']) ? json_decode(stripslashes($_COOKIE['guest_location']), true) : []);

        if (!empty($location['full'])) {
            echo esc_html($location['full']);
        }


        ?>";
    if (savedAddress && savedAddress.trim() !== "" && topbar) {
        topbar.innerHTML = `<span id="saved_address" class="saved_address">${savedAddress}</span>`;
        console.log("Saved address loaded on page load:", savedAddress);
    }


    if (autocomplete) {
        // Wait for the web component to be fully defined
        customElements.whenDefined('gmp-place-autocomplete').then(() => {

            // Listen for the new 'gmp-select' event
            autocomplete.addEventListener('gmp-select', async ({ placePrediction }) => {
                const place = placePrediction.toPlace();
                await place.fetchFields({
                    fields: ['formattedAddress', 'addressComponents']
                });
                const components = place.addressComponents || [];

                const get = type =>
                    components.find(c => c.types.includes(type))?.longText || '';

                const locationData = {
                    full: place.formattedAddress,

                    // Street: building + street
                    address_1: [
                        get('premise'),
                        get('subpremise'),
                        get('street_number'),
                        get('route')
                    ].filter(Boolean).join(', '),

                    // Apartment / landmark / society
                    address_2: [
                        get('sublocality'),
                        get('neighborhood')
                    ].filter(Boolean).join(', '),

                    city: get('locality'),
                    state: get('administrative_area_level_1'),
                    pin: get('postal_code'),
                    country: get('country')
                };

                window.selectedPlaceData = locationData;
                window.selectedAddress = locationData.full;


            });

        });
    }

    saveBtn.addEventListener("click", function () {

        if (!window.selectedPlaceData) {
            alert("Please select or detect location");
            return;
        }

        // ✅ If approx location
        if (window.selectedPlaceData.type === "approx") {
            if (!confirm("This is approximate location. Continue?")) {
                return;
            }
        }

        console.log("Saving:", window.selectedPlaceData);

        // ✅ Update UI
        if (topbar) {
            topbar.innerHTML = `
            <span class="saved_address"
                data-location='${JSON.stringify(window.selectedPlaceData)}'>
                ${window.selectedPlaceData.full}
            </span>
        `;
        }

        // ✅ Save AJAX
        fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                action: "save_user_location",
                location: JSON.stringify(window.selectedPlaceData)
            })
        }).then(() => window.location.reload());

        document.getElementById("locationModal").style.display = "none";
    });
    });

    document.addEventListener("click", function (e) {
        const savedAddress = e.target.closest(".saved_address");

        if (!savedAddress) return;

        console.log("Saved address clicked");

        const modal = document.getElementById("locationModal");
        if (!modal) {
            console.error("locationModal not found");
            return;
        }

        modal.style.display = "flex";
    });


    setTimeout(() => {
        document.querySelectorAll(".saved_address").forEach(el => {
            const fullText = el.textContent;
            el.setAttribute("data-full", fullText);
            if (fullText.length > 15) {
                el.textContent = fullText.substring(0, 35) + "...";
            }
        });
    }, 500); // waits 500ms before running

</script>


<script>
    const result = document.getElementById("result");
    const renderLocation = ({ city, state, postcode }) => {
        result.innerHTML = `${city}, ${state} - ${postcode || ''}`;
    };

    const services = [
        { url: 'http://ip-api.com/json/', parse: d => ({ city: d.city, state: d.regionName, postcode: d.zip }) },
        { url: 'https://ipapi.co/json/', parse: d => ({ city: d.city, state: d.region, postcode: d.postal }) },
        { url: 'https://ipwho.is/', parse: d => ({ city: d.city, state: d.region, postcode: d.postal }) }
    ];

    const getLocationByIP = (index = 0) => {
        if (index >= services.length) {
            result.textContent = "";
            return;
        }

        fetch(services[index].url)
            .then(res => res.json())
            .then(data => {
                const loc = services[index].parse(data);
                if (loc.city && loc.state) {
                    renderLocation(loc);
                } else {
                    getLocationByIP(index + 1);
                }
            })
            .catch(() => getLocationByIP(index + 1));
    };

    const reverseGeocode = (lat, lon) => {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`, {
            headers: { 'User-Agent': 'LocationApp/1.0' }
        })
            .then(res => res.json())
            .then(data => {
                const address = data.address || {};
                renderLocation({
                    city: address.city || address.town || address.village || "",
                    state: address.state || "",
                    postcode: address.postcode || ""
                });
            })
            .catch(() => getLocationByIP());
    };

    const detectLocation = () => {
        result.textContent = "Detecting...";
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => reverseGeocode(pos.coords.latitude, pos.coords.longitude),
                () => getLocationByIP(),
                { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
            );
        } else {
            getLocationByIP();
        }
    };

    window.onload = detectLocation;

    // Quick view carousel init yith-quick-view-content

    jQuery(document).on('yith_quick_view_loaded', function () {

        var $carousel = jQuery('#yith-quick-view-content .product-images-container');

        if ($carousel.length && typeof Flickity !== 'undefined') {

            // Destroy old instance if exists
            if ($carousel.data('flickity')) {
                $carousel.flickity('destroy');
            }

            // Init Flickity
            $carousel.flickity({
                cellSelector: '.image-item',
                wrapAround: true,
                prevNextButtons: true,
                pageDots: true,
                imagesLoaded: true,
                adaptiveHeight: true
            });
        }

    });

    document.addEventListener("DOMContentLoaded", function () {

        const currentBtn = document.getElementById("useCurrentLocation");

        if (!currentBtn) return;

        currentBtn.addEventListener("click", function () {

            currentBtn.innerText = "Detecting...";
            currentBtn.disabled = true;

            fetch('https://ipapi.co/json/')
                .then(res => res.json())
                .then(data => {

                    const locationData = {
                        full: `${data.city || ''}, ${data.region || ''} - ${data.postal || ''}, ${data.country_name || ''}`,
                        address_1: "",
                        address_2: "",
                        city: data.city || "",
                        state: data.region || "",
                        pin: data.postal || "",
                        country: data.country_name || "",
                        type: "approx"
                    };
                    console.log(locationData);
                    // ✅ Save globally
                    window.selectedPlaceData = locationData;
                    window.selectedAddress = locationData.full;

                    // ✅ Show in input
                    const input = document.querySelector(".location-search");

                    if (input) {
                        input.value = locationData.full;
                        input.focus();
                    }

                    currentBtn.innerText = "Location Detected";

                })
                .catch(() => {
                    alert("Unable to detect location");
                    currentBtn.innerText = "Use Current Location";
                    currentBtn.disabled = false;
                });

        });

    });

    jQuery(document).ready(function ($) {
    
    // 1. Trigger Lightbox on Image Click
    $(document).on('click', '.image-item img', function (e) {
        e.preventDefault();
        
        const imgSrc = $(this).attr('src');
        
        const lightboxHTML = `
            <div id="dynamic-zoom-lightbox">
                <div class="lightbox-overlay"></div>
                <span class="close-lightbox">&times;</span>
                <div class="zoom-container">
                    <img src="${imgSrc}" class="zoom-img" id="active-zoom-img">
                </div>
            </div>
        `;

        $('body').append(lightboxHTML).addClass('lightbox-open');
        
        // Animate Fade In
        setTimeout(() => $('#dynamic-zoom-lightbox').addClass('is-active'), 10);
    });

    // 2. The "Zoom Impact" logic inside the Lightbox
    $(document).on('mousemove', '.zoom-container', function (e) {
        const $img = $(this).find('img');
        const { left, top, width, height } = this.getBoundingClientRect();
        
        // Calculate mouse position in percentage
        const x = ((e.clientX - left) / width) * 100;
        const y = ((e.clientY - top) / height) * 100;

        $img.css({
            'transform-origin': `${x}% ${y}%`,
            'transform': 'scale(2.5)' // Increase this number for more zoom
        });
    });

    // Reset zoom when mouse leaves the image area
    $(document).on('mouseleave', '.zoom-container', function () {
        $(this).find('img').css('transform', 'scale(1)');
    });

    // 3. Close Lightbox
    $(document).on('click', '.close-lightbox, .lightbox-overlay', function () {
        $('#dynamic-zoom-lightbox').removeClass('is-active');
        setTimeout(() => {
            $('#dynamic-zoom-lightbox').remove();
            $('body').removeClass('lightbox-open');
        }, 300);
    });
});

</script>




<div class="location-modal" id="locationModal">
    <div class="location-popup">

        <div class="popup-header">
            <h3>Add Your Location</h3>
            <span class="close-popup" id="closeLocationPopup">&times;</span>
        </div>

        <div class="popup-body">
            <div class="search-group">
                <gmp-place-autocomplete class="location-search" id="locationSearch"></gmp-place-autocomplete>
            </div>
        </div>

        <div class="popup-footer">
            <button class="save-btn">Save Address</button>
        </div>

        <div class="current-location-wrap" style="margin-top:10px;">
            <button type="button" id="useCurrentLocation" class="save-btn">
                Use Current Location
            </button>
        </div>

    </div>
</div>

<?php wp_footer(); ?>

</body>

</html>

<style>
/* Lightbox Container */
#dynamic-zoom-lightbox {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    visibility: hidden;
}

#dynamic-zoom-lightbox.is-active {
    opacity: 1;
    visibility: visible;
}

.lightbox-overlay {
    position: absolute;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(8px);
}

/* The Zoom Window */
.zoom-container {
    position: relative;
    width: 80vw;
    height: 80vh;
    overflow: hidden; /* This is crucial for the zoom impact */
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: crosshair;
}

.zoom-img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.1s ease-out; /* Keeps zoom following mouse smoothly */
}

.close-lightbox {
    position: absolute;
    top: 20px; right: 30px;
    color: white; font-size: 50px;
    cursor: pointer; z-index: 10;
}

/* Prevent scrolling when lightbox is open */
body.lightbox-open { overflow: hidden; }
</style>


