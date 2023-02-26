import './styles/app.scss';
import 'flowbite';


const swiper = new Swiper(".swiper", {
    // Optional parameters
    direction: "horizontal",
    speed: 400,
    slidesPerView: 1,
    spaceBetween: 30,
    centeredSlides: true,

    breakpoints: {
        768: {
            slidesPerView: 3,
        },
        1024: {
            slidesPerView: 4,
        }

    },

    // Navigation arrows
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev"
    }
});