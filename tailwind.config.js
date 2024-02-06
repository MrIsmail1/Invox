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
      borderRadius: {
        xxl: "var(--border-radius-xxl)",
        xl: "var(--border-radius-xl)",
        lg: "var(--border-radius-lg)",
        md: "var(--border-radius-md)",
      },
      colors: {
        "pr-grey": withOpacity("--color-pr-grey"),
        "sd-blue": withOpacity("--color-sd-blue"),
        "white-font": withOpacity("--color-white-font"),
        "black-font": withOpacity("--color-black-font"),
        "link-color": withOpacity("--color-link-color"),
        "svg-color": withOpacity("--color-svg-color"),
        "dark-pr-grey": "#23303F",
        "text-succeed": withOpacity("--color-text-succeed"),
        "bg-succeed": withOpacity("--color-bg-succeed"),
        "border-color": withOpacity("--border-color"),
      },
      backgroundColor: {
        "bg-button": withOpacity("--color-bg-button"),
        "bg-black": withOpacity("--color-bg-black"),
        "bg-white": withOpacity("--color-bg-white"),
        "bg-succeed": withOpacity("--color-bg-succeed"),
        "bg-button-white": withOpacity("--color-bg-button-white"),
        "pr-blue": withOpacity("--color-pr-blue"),
        "dark-pr-blue": "#23303F",
        "dark-bg-button": "#1A222C",
        "dark-bg-succeed": "#66E865",
        "bg-hover-light": withOpacity("--bg-hover-light"),
        "bg-hover-sidebar": withOpacity("--bg-hover-sidebar"),
      },
      borderWidth: {
        "border-width": "var(--border-width)",
      },
      spacing: {
        84: "21rem",
        88: "22rem",
      },
      screens: {
        xs: "320px",
      },
      fontSize: {
        md: "0.9rem",
      },
    },
  },
};
