/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: ['./**/*.php', './**/*.js'],
  theme: {
    extend: {
      backgroundColor: {
        'dark': '#1e1e20',
        'dark-card': '#252529',
      }
    },
  },
  plugins: [],
}

