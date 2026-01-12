/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./assets/js/**/*.js"
  ],
  theme: {
    extend: {
      fontFamily: {
        'outfit': ['Outfit', 'sans-serif']
      },
      colors: {
        primary: {
          50: '#faf5ff',
          100: '#f3e8ff',
          200: '#e9d5ff',
          300: '#d8b4fe',
          400: '#c084fc',
          500: '#a855f7',
          600: '#9333ea',
          700: '#7e22ce',
          800: '#6b21a8',
          900: '#581c87',
        },
        secondary: {
          50: '#f0fdf4',
          100: '#dcfce7',
          200: '#bbf7d0',
          300: '#86efac',
          400: '#4ade80',
          500: '#22c55e',
          600: '#16a34a',
          700: '#15803d',
          800: '#166534',
          900: '#14532d',
        },
        // Accent - Pink untuk gradien dan highlighs
        accent : {
          50: '#fdf2f8',
          100: '#fce7f3',
          200: '#fbcfe8',
          300: '#f9a8d4',
          400: '#f472b6',
          500: '#ec4899',   // Default accent
          600: '#db2777',
          700: '#be185d',
          800: '#9d174d',
          900: '#831843',
        },
        // Royal Blue - Untuk Login/Register
        royal : {
           50: '#eef2ff',
          100: '#e0e7ff',
          200: '#c7d2fe',
          300: '#a5b4fc',
          400: '#818cf8',
          500: '#6366f1',
          600: '#4f46e5',   // Default royal (Login button)
          700: '#4338ca',
          800: '#3730a3',
          900: '#312e81',
        },
        dark: {
            50: '#f9fafb',
          100: '#f3f4f6',
          200: '#e5e7eb',
          300: '#d1d5db',
          400: '#9ca3af',
          500: '#6b7280',
          600: '#4b5563',
          700: '#374151',
          800: '#1f2937',
          900: '#111827',
        },
        bubble: {
        pink: '#fce7f3',
        purple: '#e9d5ff',
        blue: '#dbeafe',
        cyan: '#cffafe',
      },
      glass:{
        white: 'rgba(255, 255, 255, 0.7)',
        border: 'rgba(255, 255, 255, 0.3)',
        dark : 'rgba(0, 0, 0, 0.1)',
      }

      },

      borderRadius: {
        'xl': '12px',
        '2xl': '16px',
        '3xl':'24px',
        '4xl': '32px',
      },
      // BOX SHADOW
      boxShadow: {
         'glass': '0 8px 32px rgba(0, 0, 0, 0.1)',
        'glass-lg': '0 12px 48px rgba(0, 0, 0, 0.15)',
        'soft': '0 4px 20px rgba(0, 0, 0, 0.08)',
        'soft-lg': '0 8px 30px rgba(0, 0, 0, 0.12)',
        'glow-purple': '0 10px 40px rgba(124, 58, 237, 0.3)',
        'glow-pink': '0 10px 40px rgba(236, 72, 153, 0.3)',
      },
      // Backdrop Blur
      backdropBlur: {
        'xs': '2px',
        'glass': '10px',
      },
      
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'float': 'float 20s ease-in-out infinite',
        'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0) scale(1)' },
          '50%': {transform: 'translateY(-20px) scale(1.05)'},
        },
        pulseSoft: {
          '0%, 100%' : {opacity: '1'},
          '50%' : {opacity : '0.7'},
        }
      },
      // Gradient Background
      backgroundImage: {
        // User Dashboard background
        'gradient-user': 'linear-gradient(135deg, #FFB6F3 0%, #C8B6FF 50%, #A5B4FC 100%)',
        // Admin sidebar gradien
        'gradient-admin' : 'linear-gradient(180deg, #EC4899 0%, #8B5CF6 100%)',
        // Primary button gradient
        'gradient-primary' : 'linear-gradient(135deg, #7C3AED 0%, #EC4899 100%)',
        // Stats card decorative blob
        'gradient-blob' : 'linear-gradient(135deg, #C8B6FF 0%, #FFB6F3 100%)',
      },
    },
  },
  plugins: [],
}
