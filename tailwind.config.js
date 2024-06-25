/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./index.html', './assets/js/**/*.js'],
  theme: {
    extend: {
      screens: {
        'xs': '0px',     // Custom for extra small (optional, Tailwind starts from mobile-first)
        'sm': '576px',   // Small devices (≥576px)
        'md': '768px',   // Medium devices (≥768px)
        'lg': '992px',   // Large devices (≥992px)
        'xl': '1200px',  // Extra large devices (≥1200px)
        '2xl': '1400px', // Extra extra large devices (≥1400px)
      }
    },
  },
  plugins: [],
}
