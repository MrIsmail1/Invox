/** @type {import('tailwindcss').Config} */

function withOpacity(variableName) {
  return ({ opacityValue }) => {
    if (opacityValue !== undefined) {
      return `rgba(var(${variableName}), ${opacityValue})`;
    }
    return `rgb(var(${variableName}))`;
  };
}

module.exports = {
  content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "pr-grey": withOpacity("--color-pr-grey"),
        "pr-blue": withOpacity("--color-pr-blue"),
        "sd-blue": withOpacity("--color-sd-blue"),
        "bg-button": withOpacity("--color-bg-button"),
        "bg-button-white": withOpacity("--color-bg-button-white"),
        "white-font": withOpacity("--color-white-font"),
        "black-font": withOpacity("--color-black-font"),
        "bg-black": withOpacity("--color-bg-black"),
        "link-color": withOpacity("--color-link-color"),
        "svg-color": withOpacity("--color-svg-color"),
      },
      fontFamily: {
        Satoshi: ["Satoshi", "sans-serif"],
      },
      spacing: {
        84: "21rem",
        88: "22rem",
      },
      borderRadius: {
        md: "10px",
      },
      backgroundColor: {
        skin: {
          fill: withOpacity("--color-fill"),
          "button-accent": withOpacity("--color-button-accent"),
          "button-accent-hover": withOpacity("--color-button-accent-hover"),
          "button-muted": withOpacity("--color-button-muted"),
        },
      },
      screens: {
        xs: "320px",
      },
    },
  },
};
