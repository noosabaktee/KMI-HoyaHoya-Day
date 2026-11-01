<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Hoya-Hoya Day Festival' }} - PT Kalbe Morinaga Indonesia</title>
  
  <!-- Tailwind CSS & Google Fonts -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- HTML5 QR Code Scanner -->
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            kmi: {
              50: '#f0fdf4',
              100: '#dcfce7',
              200: '#bbf7d0',
              300: '#86efac',
              400: '#4ade80',
              500: '#22c55e',
              600: '#008542', /* Official Kalbe Green */
              700: '#006d36',
              800: '#065f46',
              900: '#022c22',
            },
            morinaga: {
              50: '#ecfeff',
              500: '#06b6d4',
              600: '#0891b2',
              700: '#0e7490',
            },
            festive: {
              500: '#f59e0b',
              600: '#d97706',
            }
          },
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          }
        }
      }
    }
  </script>

  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    .glass-card { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
    @media print {
      .no-print { display: none !important; }
      body { background: white !important; }
    }
  </style>
  @stack('styles')
</head>
<body class="text-slate-800 antialiased min-h-screen flex flex-col bg-slate-50">

  <!-- Feedback / Alert Notifications -->
  @include('partials.alerts')

  <div class="flex-1 flex overflow-hidden h-screen">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Top Navbar -->
      @include('partials.navbar')

      <!-- Page Main Body -->
      <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
        @yield('content')
      </main>
    </div>
  </div>

  <!-- Global Number & Currency Separator Utility -->
  <script>
    function formatNumberWithSeparator(val) {
      if (val === null || val === undefined || val === '') return '';
      const digits = String(val).replace(/\D/g, '');
      if (!digits) return '';
      return new Intl.NumberFormat('id-ID').format(Number(digits));
    }

    function parseRawNumber(val) {
      if (!val) return 0;
      const digits = String(val).replace(/\D/g, '');
      return digits ? Number(digits) : 0;
    }

    function attachNumberSeparator(input) {
      if (!input || input.dataset.separatorInitialized === 'true') return;
      input.dataset.separatorInitialized = 'true';

      // Set inputmode numeric so virtual keyboard stays numeric
      if (!input.getAttribute('inputmode')) {
        input.setAttribute('inputmode', 'numeric');
      }

      // Initial formatting
      if (input.value) {
        input.value = formatNumberWithSeparator(input.value);
      }

      input.addEventListener('input', function () {
        const originalValue = input.value;
        const originalCursor = input.selectionEnd || originalValue.length;

        // Count how many numeric digits were before the cursor
        const digitsBeforeCursor = originalValue.slice(0, originalCursor).replace(/\D/g, '').length;

        const rawDigits = originalValue.replace(/\D/g, '');
        if (!rawDigits) {
          input.value = '';
          return;
        }

        const formatted = new Intl.NumberFormat('id-ID').format(Number(rawDigits));
        input.value = formatted;

        // Restore cursor position based on digits count
        let newCursor = 0;
        let count = 0;
        for (let i = 0; i < formatted.length; i++) {
          if (/\d/.test(formatted[i])) {
            count++;
          }
          if (count === digitsBeforeCursor) {
            newCursor = i + 1;
            break;
          }
        }
        if (count < digitsBeforeCursor) {
          newCursor = formatted.length;
        }

        input.setSelectionRange(newCursor, newCursor);
      });

      input.addEventListener('blur', function () {
        if (input.value) {
          input.value = formatNumberWithSeparator(input.value);
        }
      });
    }

    function initAllNumberSeparators() {
      // Find all designated inputs
      const selector = '.number-separator, [data-number-format], input[data-type="currency"]';
      document.querySelectorAll(selector).forEach(attachNumberSeparator);

      // Convert any standard type="number" to type="text" with inputmode="numeric" to support dots
      document.querySelectorAll('input[type="number"]').forEach(input => {
        try {
          input.type = 'text';
          input.setAttribute('inputmode', 'numeric');
          input.classList.add('number-separator');
          attachNumberSeparator(input);
        } catch (_) {}
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initAllNumberSeparators);
    } else {
      initAllNumberSeparators();
    }

    // Auto-clean before form submit so backend receives clean numeric digits
    document.addEventListener('submit', function (e) {
      const form = e.target;
      if (!form || !(form instanceof HTMLFormElement)) return;
      form.querySelectorAll('.number-separator, [data-number-format]').forEach(input => {
        if (input.value && !input.dataset.skipCleanOnSubmit) {
          input.value = input.value.replace(/\D/g, '');
        }
      });
    }, true);
  </script>

  @stack('scripts')
</body>
</html>
