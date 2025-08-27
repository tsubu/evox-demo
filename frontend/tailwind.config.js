/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        'sevenbar': ['SevenBar', 'monospace'],
      },
      colors: {
        'evox-blue': '#00AFB6',
        'evox-blue-dark': '#0081C8',
        'evox-gray': '#1a1a1a',
        'evox-black': '#0a0a0a',
        'evox-gold': '#ffd700',
        'evox-dark': '#000000',
      },
      animation: {
        'float': 'float 3s ease-in-out infinite',
        'pulse-slow': 'pulse 2s ease-in-out infinite',
        'shake': 'shake 0.5s ease-in-out',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0px)' },
          '50%': { transform: 'translateY(-10px)' },
        },
        shake: {
          '0%, 100%': { transform: 'translateX(0)' },
          '25%': { transform: 'translateX(-5px)' },
          '75%': { transform: 'translateX(5px)' },
        },
      },
      backgroundImage: {
        'hero-pattern': "url('../images/hero.png')",
        'footer-pattern': "url('../images/footerimage.png')",
      },
    },
  },
  plugins: [
    function({ addComponents }) {
      addComponents({
        '.btn-primary': {
          '@apply bg-evox-blue hover:bg-evox-blue-dark text-white font-bold py-2 px-4 rounded transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-evox-blue focus:ring-opacity-50': {},
        },
        '.btn-secondary': {
          '@apply bg-evox-gray hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50': {},
        },
        '.link-hover': {
          '@apply transition-all duration-300 transform hover:scale-105': {},
        },
        '.link-slide': {
          '@apply relative overflow-hidden': {},
          '&::after': {
            '@apply content-[""] absolute bottom-0 left-0 w-0 h-0.5 bg-evox-blue transition-all duration-300': {},
          },
          '&:hover::after': {
            '@apply w-full': {},
          },
        },
      })
    }
  ],
}
