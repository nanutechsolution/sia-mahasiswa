import forms from '@tailwindcss/forms'

export default {
      presets: [
        require("./vendor/power-components/livewire-powergrid/tailwind.config.js"), 
    ],
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './app/Livewire/**/*Table.php',
        './vendor/power-components/livewire-powergrid/resources/views/**/*.php',
        './vendor/power-components/livewire-powergrid/src/Themes/Tailwind.php'
    ],
    plugins: [
        forms,
    ],
}
