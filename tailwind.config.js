/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')

module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    colors: {
      'primary-red': '#76004F',
      'primary-gray': '#F5F5F5',
      black: colors.black,
      white: colors.white,
      gray: colors.gray,
      yellow: colors.yellow,
      blue: colors.blue,
      red: colors.red,
      green: colors.green,
    },
    extend: {
      backgroundImage: {
        'login': "url('/public/images/bg-login.png')",
      },
    },
    fontFamily: {
      'raleway': ['Raleway', 'sans-serif'],
    }
  },
  plugins: [],
}
