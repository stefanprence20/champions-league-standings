<template>
  <div class="flex flex-col sm:flex-row items-center justify-center gap-4 p-6">
    <button 
      @click="$emit('play-week')"
      :disabled="currentWeek >= totalWeeks"
      class="w-full sm:w-auto px-8 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white text-base font-semibold rounded"
    >
      Play Week
    </button>
    <button 
      @click="$emit('play-all')"
      :disabled="currentWeek >= totalWeeks"
      class="w-full sm:w-auto px-8 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white text-base font-semibold rounded"
    >
      Play All
    </button>
    <button 
      @click="$emit('reset')"
      class="w-full sm:w-auto px-8 py-2 bg-red-600 hover:bg-red-700 text-white text-base font-semibold rounded"
    >
      Reset League
    </button>
  </div>
</template>

<script>
export default {
  name: 'ActionButtons',
  props: {
    currentWeek: {
      type: Number,
      default: 0
    },
    totalWeeks: {
      type: Number,
      default: 6
    }
  },
  emits: ['play-week', 'play-all', 'reset']
}
</script> 