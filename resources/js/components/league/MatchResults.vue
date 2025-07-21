<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-base font-semibold text-gray-800">Match Results</h2>
    </div>
    
    <div class="px-6 bg-gray-50 border-b border-gray-200">
      <h3 class="text-sm font-medium text-gray-700">Week {{ getWeekText(currentWeek) }} result</h3>
    </div>
    
    <div class="overflow-y-auto">
      <div v-if="currentWeekGames.length === 0" class="flex flex-col items-center justify-center py-12 px-6">
        <p class="text-gray-500 text-xs text-center">Click Play Week to start the simulation.</p>
      </div>
      
      <div v-else class="divide-y divide-gray-200">
        <div 
          v-for="game in currentWeekGames" 
          :key="game.id" 
          class="p-3 bg-gray-50 transition-colors"
        >
          <div class="flex items-center justify-between mb-2">
            <div class="flex-1 text-right">
              <span class="font-medium text-gray-900 text-sm">{{ game.team1.name }}</span>
            </div>
            
            <div 
              class="mx-3 px-3 bg-blue-100 rounded-lg cursor-pointer hover:bg-blue-200 transition-colors"
              @click="$emit('edit-game', game)"
            >
              <span class="font-bold text-blue-800 text-base">
                {{ game.team1_goals }} - {{ game.team2_goals }}
              </span>
            </div>
            
            <div class="flex-1 text-left">
              <span class="font-medium text-gray-900 text-sm">{{ game.team2.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MatchResults',
  props: {
    games: {
      type: Array,
      required: true
    },
    currentWeek: {
      type: Number,
      required: true
    }
  },
  computed: {
    currentWeekGames() {
      return this.games.filter(game => game.week === this.currentWeek)
    }
  },
  methods: {
    getWeekText(week) {
      if (week === 1) return '1st'
      if (week === 2) return '2nd'
      if (week === 3) return '3rd'
      return week + 'th'
    }
  },
  emits: ['edit-game']
}
</script> 