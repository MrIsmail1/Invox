/** @type {import('tailwindcss').Config} */

module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        'pr-blue': '#364959',
        'pr-grey': '#A2AFBC',
        'sd-blue': '#5A6B7A',
        'color-button': 'var(--color-button)',
        'color-sidebar': 'var(--color-sidebar)',
      },
      fontFamily: {
        'Satoshi': ['Satoshi', 'sans-serif'],
      },
      spacing: {
        '84': '21rem',
        '88': '22rem',
      },
      borderRadius: {
        'md': '10px',
      },
      backgroundColor: {
        background: 'var(--background-color)',
      },
    },
  },
}

