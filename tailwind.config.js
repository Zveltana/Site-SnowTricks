/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/**/*.twig"],
  theme: {
    extend: {
      height: {
        128: "32rem",
      }
    },
    fontFamily: {
      oswald: ["Oswald"],
      dr_sugiyama: ["Dr Sugiyama"],
    },
  },
  plugins: [],
}
