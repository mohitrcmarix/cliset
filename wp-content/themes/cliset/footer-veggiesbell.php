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

        // function disableLinks() {

        //     $('.product-images-container .image-item a').each(function () {

        //         $(this).attr('href', 'javascript:void(0)');

        //     });

        // }

        // Run on load

        // disableLinks();

        // Run again after slider change (important)

        // $('.product-images-container').on('select.flickity', function () {

        //     setTimeout(disableLinks, 100);

        // });

    });



    // jQuery(document).ready(function ($) {

    //     function updateBackground() {
    //         console.log("Updating background image for quick view");
    //         // Get current active image
    //         var activeImg = $('.product-images .is-selected img');

    //         if (activeImg.length) {
    //             console.log("in script");
    //             var imgSrc = activeImg.attr('src');

    //             // Apply background to container
    //             $('.product-images').css({
    //                 'background-image': 'url(' + imgSrc + ')',
    //                 'background-size': 'cover',
    //                 'background-position': 'center',
    //                 'background-repeat': 'no-repeat'
    //             });
    //         }
    //     }

    //     // Initial load
    //     updateBackground();

    //     // On slide change (Flickity event)
    //     $('.product-images-container').on('change.flickity', function () {
    //         setTimeout(function () {
    //             updateBackground();
    //         }, 100); // small delay to ensure class update
    //     });

    // });


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
    // document.addEventListener("DOMContentLoaded", function () {
    // const input = document.querySelector(".location-search");

    // const autocomplete = new google.maps.places.Autocomplete(input, {
    //     types: ['geocode'],
    //     fields: [
    //         'place_id',
    //         'formatted_address',
    //         'geometry',
    //         'address_components'
    //     ]
    // });

    // autocomplete.addListener("place_changed", function () {
    //     const place = autocomplete.getPlace();

    //     if (!place.geometry) {
    //         console.log("No details available");
    //         return;
    //     }

    //     console.log("Address:", place.formatted_address);
    //     console.log("Lat:", place.geometry.location.lat());
    //     console.log("Lng:", place.geometry.location.lng());
    // });
    // });
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


                // const components = place.addressComponents || [];

                // const get = type =>
                // components.find(c => c.types.includes(type))?.longText || '';

                // const locationData = {
                // full: place.formattedAddress,
                // address: get('route'),
                // city: get('locality'),
                // state: get('administrative_area_level_1'),
                // pin: get('postal_code'),
                // country: get('country')
                // };

                // selectedAddress = locationData.full;
                // selectedPlaceData = locationData;

                // Fetch fields from the selected place
                // console.log(place);
                // await place.fetchFields({ fields: ['displayName', 'formattedAddress', 'location'] });

                // console.log(place.displayName.text); 
                //selectedPlaceData = place.toJSON();
                // Save formattedAddress and full place info
                // console.log("User selected address:", selectedAddress);
                // console.log("Full place object:", selectedPlaceData);
                // const title = selectedPlaceData.displayName || selectedPlaceData.formattedAddress || "";

                //console.log("Title / Display Name:", title);

                //  selectedAddress = title + ' ' + place.formattedAddress || place.displayName || "";



                // Optional: show somewhere in the modal for debugging
                // selectedPlaceTitle.textContent = 'Selected Place:';
                // selectedPlaceInfo.textContent = JSON.stringify(selectedPlaceData, null, 2);
            });

        });
    }

    // Save button click
    // saveBtn.addEventListener("click", function () {


    //     if (!selectedAddress) {
    //         console.log("No address selected from suggestions");
    //         alert("Please select an address from suggestions");
    //         return;
    //     }

    //     console.log("Final address to save:", selectedAddress);

    //     // Update topbar
    //     if (topbar) {
    //         topbar.innerHTML = `
    //             <span class="saved_address"
    //                     data-location='${JSON.stringify(selectedPlaceData)}'>
    //                 ${selectedPlaceData.full}
    //             </span>
    //             `;

    //         console.log("Topbar updated with address");
    //     }


    //     // Save to backend
    //     fetch("<?php echo admin_url('admin-ajax.php'); ?>", {
    //         method: "POST",
    //         headers: { "Content-Type": "application/x-www-form-urlencoded" },
    //         body: new URLSearchParams({
    //             action: "save_user_location",
    //             location: JSON.stringify(selectedPlaceData)
    //         })
    //     }).then(() => window.location.reload());

    //     // Close modal
    //     document.getElementById("locationModal").style.display = "none";
    //     console.log("Modal closed");
    // });
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

    // document.addEventListener("DOMContentLoaded", function () {

    //     const currentBtn = document.getElementById("useCurrentLocation");

    //     if (!currentBtn) return;

    //     currentBtn.addEventListener("click", function () {

    //         currentBtn.innerText = "Detecting...";
    //         currentBtn.disabled = true;

    //         fetch('https://ipapi.co/json/')
    //             .then(res => res.json())
    //             .then(data => {

    //                 const locationData = {
    //                     full: `${data.city || ''}, ${data.region || ''} - ${data.postal || ''}, ${data.country_name || ''}`,
    //                     address_1: "",
    //                     address_2: "",
    //                     city: data.city || "",
    //                     state: data.region || "",
    //                     pin: data.postal || "",
    //                     country: data.country_name || "",
    //                     type: "approx"
    //                 };
    //                 console.log(locationData);
    //                 // ✅ Save globally
    //                 window.selectedPlaceData = locationData;
    //                 window.selectedAddress = locationData.full;

    //                 // ✅ Show in input
    //                 const input = document.querySelector(".location-search");

    //                 if (input) {
    //                     input.value = locationData.full;
    //                     input.focus();
    //                 }

    //                 currentBtn.innerText = "Location Detected";

    //             })
    //             .catch(() => {
    //                 alert("Unable to detect location");
    //                 currentBtn.innerText = "Use Current Location";
    //                 currentBtn.disabled = false;
    //             });

    //     });

    // });


function getBestAddress(lat, lon) {

    const points = [
        [lat, lon],
        [lat + 0.0005, lon],
        [lat - 0.0005, lon],
        [lat, lon + 0.0005],
        [lat, lon - 0.0005]
    ];

    //  Reset results every time
    let results = [];
    let completed = 0;

    points.forEach(([lt, ln]) => {

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lt}&lon=${ln}&addressdetails=1`)
            .then(res => res.json())
            .then(data => {

                const addr = data.address || {};

                if (addr.postcode) {
                    results.push({
                        full: data.display_name,
                        addr: addr,
                        priority: addr.road ? 2 : 1
                    });
                }

            })
            .catch(() => {})
            .finally(() => {

                completed++;

                //  Wait for ALL requests complete
                if (completed === points.length) {

                    if (results.length === 0) {
                        alert("Address not found");
                        return;
                    }

                    //  Sort best result
                    results.sort((a, b) => b.priority - a.priority);

                    const best = results[0];

                    //  Always use correct addr
                    setAddress(best.full, best.addr);
                }

            });

    });
}
function setAddress(fullAddress, addr) {

    window.selectedAddress = fullAddress;
    window.selectedPlaceData = {
        full: fullAddress,
        city: addr.city || addr.town || addr.village || "",
        state: addr.state || "",
        pin: addr.postcode || "",
        country: addr.country || ""
    };

    const input = document.querySelector(".location-search");
    if (input) {
        input.value = fullAddress;
    }

    const btn = document.getElementById("useCurrentLocation");
    if (btn) {
        btn.innerText = "Location Detected";
        btn.disabled = false;
    }
}
document.addEventListener("DOMContentLoaded", function () {

    const btn = document.getElementById("useCurrentLocation");
    if (!btn) return;

    btn.addEventListener("click", function () {

        btn.innerText = "Detecting...";
        btn.disabled = true;

        if (!navigator.geolocation) {
            alert("Geolocation not supported");
            return;
        }

        navigator.geolocation.getCurrentPosition(

           function (pos) {
                const lat = pos.coords.latitude;
                const lon = pos.coords.longitude;
                getBestAddress(lat, lon);
            },

            function () {
                alert("Permission denied or location failed");
                btn.innerText = "Use Current Location";
                btn.disabled = false;
            },

            { enableHighAccuracy: true, timeout: 10000 }

        );

    });

});
// 	jQuery(document).ready(function ($) {
    
//     $(document).on('click', '.image-item', function (e) {
//         e.preventDefault();
        
//         // 1. DYNAMIC QUALITY FIX: 
//         // We look for the 'href' in the <a> tag first, because that is the Full Resolution image.
//         // If not found, we fallback to the 'src' of the <img>.
//         const highResImg = $(this).find('a').attr('href') || $(this).find('img').attr('src');
        
//         const lightboxHTML = `
//             <div id="dynamic-zoom-lightbox">
//                 <div class="lightbox-overlay"></div>
//                 <span class="close-lightbox">&times;</span>
//                 <div class="zoom-container">
//                     <img src="${highResImg}" class="zoom-img" id="active-zoom-img">
//                 </div>
//                 <div class="loading-spinner">Loading High Res...</div>
//             </div>
//         `;

//         $('body').append(lightboxHTML).addClass('lightbox-open');
        
//         // Hide spinner once high-res image loads
//         $('#active-zoom-img').on('load', function() {
//             $('.loading-spinner').hide();
//         });

//         setTimeout(() => $('#dynamic-zoom-lightbox').addClass('is-active'), 10);
//     });

//     // ... (Keep the mousemove and close logic from the previous step)
// });

// 	jQuery(document).ready(function ($) {
    
//     $(document).on('click', '.image-item', function (e) {
//         e.preventDefault();
        
//         // 1. DYNAMIC QUALITY FIX: 
//         // We look for the 'href' in the <a> tag first, because that is the Full Resolution image.
//         // If not found, we fallback to the 'src' of the <img>.
//         const highResImg = $(this).find('a').attr('href') || $(this).find('img').attr('src');
        
//         const lightboxHTML = `
//             <div id="dynamic-zoom-lightbox">
//                 <div class="lightbox-overlay"></div>
//                 <span class="close-lightbox">&times;</span>
//                 <div class="zoom-container">
//                     <img src="${highResImg}" class="zoom-img" id="active-zoom-img">
//                 </div>
//                 <div class="loading-spinner">Loading High Res...</div>
//             </div>
//         `;

//         $('body').append(lightboxHTML).addClass('lightbox-open');
        
//         // Hide spinner once high-res image loads
//         $('#active-zoom-img').on('load', function() {
//             $('.loading-spinner').hide();
//         });

//         setTimeout(() => $('#dynamic-zoom-lightbox').addClass('is-active'), 10);
//     });

//     // ... (Keep the mousemove and close logic from the previous step)
// });

jQuery(document).ready(function ($) {

    // 1. OPEN LIGHTBOX WITH HIGH-RES IMAGE
    $(document).on('click', '.image-item', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Target the <a> tag's href for maximum quality
        const highResUrl = $(this).find('a').attr('href');
        
        if (!highResUrl) return;

        const lightboxHTML = `
            <div id="premium-zoom-lightbox">
                <div class="lb-bg"></div>
                <div class="lb-controls">
                    <span class="lb-close">&times;</span>
                    <p class="lb-hint">Move mouse to zoom</p>
                </div>
                <div class="lb-viewport">
                    <img src="${highResUrl}" class="lb-native-image" id="zoom-target">
                </div>
                <div class="lb-loader">Loading High Quality...</div>
            </div>
        `;

        $('body').append(lightboxHTML).addClass('no-scroll');

        // Hide loader when the big file is ready
        $('#zoom-target').on('load', function() {
            $('.lb-loader').fadeOut();
            $(this).addClass('loaded');
        });
    });

    // 2. DYNAMIC ZOOM LOGIC
    $(document).on('mousemove', '.lb-viewport', function (e) {
        const $img = $(this).find('img.loaded');
        if (!$img.length) return;

        const { left, top, width, height } = this.getBoundingClientRect();
        
        // Calculate mouse position in %
        const x = ((e.clientX - left) / width) * 100;
        const y = ((e.clientY - top) / height) * 100;

        $img.css({
            'transform-origin': `${x}% ${y}%`,
            'transform': 'scale(2.5)' // Adjust this for zoom depth
        });
    });

    // Reset zoom on mouse leave
    $(document).on('mouseleave', '.lb-viewport', function () {
        $(this).find('img').css('transform', 'scale(1)');
    });

    // 3. CLOSE LOGIC
    $(document).on('click', '.lb-close, .lb-bg', function () {
        $('#premium-zoom-lightbox').fadeOut(300, function() {
            $(this).remove();
            $('body').removeClass('no-scroll');
        });
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

             <button type="button" id="useCurrentLocation" class="save-btn">
                Use Current Location
            </button>
        </div>

        <div class="current-location-wrap" style="margin-top:10px;">
           
        </div>

    </div>
</div>

<?php wp_footer(); ?>

</body>

</html>