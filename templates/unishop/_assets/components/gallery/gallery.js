$('[data-gallery-image]').magnificPopup({
    delegate: "[data-gallery-image-item]",
    type: "image",
    mainClass: 'gallery-default',
    gallery:{
        enabled: true,
        tCounter: '%curr% of %total%'
    },
    overflowY: "hidden",
    image: {
        titleSrc: 'data-gallery-image-title'
    }
});