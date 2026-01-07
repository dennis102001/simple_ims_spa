/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        dark: {
          pri: '#111111',
          sec: '#1C1C1C',
          ter: '#000000'
        },
        darkgray: {
          pri: '#2F2F2F',
          sec: '#3A3A3A',
          ter: '#222222'
        },
        warmyellow: {
          pri: '#FFCB74',
          sec: '#FFD892',
          ter: '#E6B65F'
        },
        light: {
          pri: '#F5F5F5',
          sec: '#EAEAEA',
          ter: '#DDDDDD'
        },
        black_secondary: '#2F2F2F',
        background_primary: '#F6F6F6',
        yellow_primary: '#FFCB74'
      },
      fontFamily: {
        sans: ['Inter', 'Poppins', 'sans-serif'],
      },
    },
  },
  plugins: [],
  safelist: [
    'bg-red-500',
    'bg-orange-500',
    'bg-yellow-500',
    'bg-green-500',
    'bg-blue-500',
    'bg-violet-500',
    'bg-pink-500',
    'bg-gray-500',
  ]
}

