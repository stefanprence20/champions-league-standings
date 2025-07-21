<template>
  <header class="relative z-20 bg-white bg-opacity-5 backdrop-blur-xl border-b border-white border-opacity-10 shadow-2xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-champions-gold-400 to-champions-gold-600 rounded-xl flex items-center justify-center shadow-lg">
              <span class="text-2xl">⚽</span>
            </div>
            <div>
              <h1 class="text-xl font-bold bg-gradient-to-r from-champions-gold-400 to-champions-gold-600 bg-clip-text text-transparent">
                Champions League Simulator
              </h1>
              <p class="text-xs text-gray-300 -mt-1">Professional Simulation</p>
            </div>
          </div>
        </div>
        
        <div class="flex items-center space-x-4">
          <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-300">
            <span>Season Progress</span>
          </div>
          <div class="bg-white bg-opacity-10 backdrop-blur-md rounded-full px-6 py-2 border border-white border-opacity-20 shadow-lg">
            <div class="flex items-center space-x-2">
              <div class="w-2 h-2 bg-champions-gold-400 rounded-full animate-pulse"></div>
              <span class="font-semibold text-champions-gold-400">
                Week {{ currentWeek }} of {{ totalWeeks }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
export default {
  name: 'AppHeader',
  props: {
    currentWeek: {
      type: Number,
      default: 0
    },
    totalWeeks: {
      type: Number,
      default: 6
    }
  }
}
</script> 