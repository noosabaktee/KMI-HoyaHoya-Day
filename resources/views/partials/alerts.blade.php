@if(session('success'))
  <div id="alertSuccess" class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200 shadow-xl transition-all duration-300">
    <i class='bx bx-check-circle text-2xl mr-3 text-emerald-600'></i>
    <div class="text-sm font-medium">{{ session('success') }}</div>
    <button type="button" onclick="this.parentElement.remove()" class="ml-4 text-emerald-600 hover:text-emerald-900 focus:outline-none">
      <i class='bx bx-x text-xl'></i>
    </button>
  </div>
  <script>setTimeout(() => document.getElementById('alertSuccess')?.remove(), 6000);</script>
@endif

@if(session('error'))
  <div id="alertError" class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-rose-800 rounded-2xl bg-rose-50 border border-rose-200 shadow-xl transition-all duration-300">
    <i class='bx bx-error-circle text-2xl mr-3 text-rose-600'></i>
    <div class="text-sm font-medium">{{ session('error') }}</div>
    <button type="button" onclick="this.parentElement.remove()" class="ml-4 text-rose-600 hover:text-rose-900 focus:outline-none">
      <i class='bx bx-x text-xl'></i>
    </button>
  </div>
  <script>setTimeout(() => document.getElementById('alertError')?.remove(), 8000);</script>
@endif

@if(session('warning'))
  <div id="alertWarning" class="fixed top-5 right-5 z-50 flex items-center p-4 mb-4 text-amber-800 rounded-2xl bg-amber-50 border border-amber-200 shadow-xl transition-all duration-300">
    <i class='bx bx-info-circle text-2xl mr-3 text-amber-600'></i>
    <div class="text-sm font-medium">{{ session('warning') }}</div>
    <button type="button" onclick="this.parentElement.remove()" class="ml-4 text-amber-600 hover:text-amber-900 focus:outline-none">
      <i class='bx bx-x text-xl'></i>
    </button>
  </div>
  <script>setTimeout(() => document.getElementById('alertWarning')?.remove(), 8000);</script>
@endif

