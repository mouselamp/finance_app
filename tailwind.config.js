module.exports = {
  purge: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/sass/**/*.sass',
    './resources/sass/**/*.scss'
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {
      colors: {
        primary: '#2563eb',
        secondary: '#64748b',
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
