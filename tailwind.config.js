/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
        "./node_modules/flowbite/**/*.js"
    ],
    theme: {
        colors: {
            "primary-red": "#76004F",
            "primary-gray": "#F5F5F5",
            "front-red": "#CC0000",
            "primary-pink": "#D24E8A",
            danger: "##F8D7DA",
            "danger-border": "#F5C6CB",
            success: "#d4edda",
            "success-border": "#C3E6CB",
            black: colors.black,
            white: colors.white,
            gray: colors.gray,
            yellow: colors.yellow,
            blue: colors.blue,
            red: colors.red,
            green: colors.green
        },
        extend: {
            backgroundImage: {
                login: "url('/public/images/bg-login.png')"
            }
        },
        fontFamily: {
            raleway: ["Raleway", "sans-serif"],
            ibm: ["IBM Plex Sans", "sans-serif"]
        }
    },
    plugins: [require("flowbite/plugin")]
};
