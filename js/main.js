function LoadAccountDropdown() {
  const accountButton = document.getElementById("account-button");
  const accountDropdown = document.getElementById("account-dropdown");

  if (accountButton && accountDropdown) {
    accountButton.addEventListener("click", function (e) {
      e.preventDefault();

      if (accountDropdown.style.display === "flex") {
        accountDropdown.style.display = "none";
      } else {
        accountDropdown.style.display = "flex";
      }
    });
  }
}

// Function to get cookie by name
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(';').shift();
  return null;
}

// Function to set cookie
function setCookie(name, value, days) {
  let expires = "";
  if (days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

// Function to toggle between dark and light mode
function toggleDarkMode() {
  const html = document.documentElement;
  const darkModeButton = document.getElementById("dark-mode");
  const darkModeIcon = darkModeButton.querySelector("img");

  // Check if currently in dark mode
  const isDarkMode = html.getAttribute("data-theme") === "dark";

  if (isDarkMode) {
    // Switch to light mode
    html.removeAttribute("data-theme");
    darkModeIcon.src = "images/sun.svg";
    setCookie("theme", "light", 365);
  } else {
    // Switch to dark mode
    html.setAttribute("data-theme", "dark");
    darkModeIcon.src = "images/moon.svg";
    setCookie("theme", "dark", 365);
  }
}

// Function to apply theme from cookie
function applyTheme() {
  const theme = getCookie("theme");
  const html = document.documentElement;
  const darkModeButton = document.getElementById("dark-mode");

  if (darkModeButton) {
    const darkModeIcon = darkModeButton.querySelector("img");

    if (theme === "dark") {
      html.setAttribute("data-theme", "dark");
      if (darkModeIcon) {
        darkModeIcon.src = "images/moon.svg";
      }
    } else {
      html.removeAttribute("data-theme");
      if (darkModeIcon) {
        darkModeIcon.src = "images/sun.svg";
      }
    }

    // Add click event listener to dark mode button
    darkModeButton.addEventListener("click", toggleDarkMode);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  LoadAccountDropdown();
  applyTheme();
});
