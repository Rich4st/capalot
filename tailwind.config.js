/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: ['./**/*.php', './**/*.js'],
  theme: {
    extend: {
      backgroundColor: {
        'dark': '#1e1e20',
        'dark-card': '#252529',
        'primary': '#570df8',
        'secondary': '#f000b8',
        'accent': '#1dcdbc',
        'info': '#3abff8',
        'success': '#36d399',
        'error': '#f87272'
      }
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}

