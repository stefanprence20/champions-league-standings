<template>
  <div v-if="show" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl border border-gray-200 shadow max-w-md w-full">
      <div class="px-6 py-4 border-b border-gray-100 rounded-t-xl">
        <h3 class="text-base font-semibold text-gray-800">Edit Match Result</h3>
      </div>
      <div class="p-6">
        <div class="text-center mb-6">
          <div class="flex items-center justify-center gap-4 text-base font-medium text-gray-800">
            <span>{{ game?.team1?.name }}</span>
            <span class="text-gray-400">vs</span>
            <span>{{ game?.team2?.name }}</span>
          </div>
        </div>
        <div class="flex items-center justify-center gap-4 mb-6">
          <input 
            v-model.number="editingScore.team1" 
            type="number" 
            min="0" 
            class="w-16 h-12 border border-gray-200 rounded text-center text-lg font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200"
            placeholder="0"
          >
          <span class="text-2xl font-bold text-gray-400">-</span>
          <input 
            v-model.number="editingScore.team2" 
            type="number" 
            min="0" 
            class="w-16 h-12 border border-gray-200 rounded text-center text-lg font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition-all duration-200"
            placeholder="0"
          >
        </div>
        <div class="flex gap-3">
          <button 
            @click="saveEdit" 
            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded transition-colors"
          >
            Save
          </button>
          <button 
            @click="$emit('close')" 
            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 rounded transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GameEditModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    game: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      editingScore: { team1: 0, team2: 0 }
    }
  },
  watch: {
    game(newGame) {
      if (newGame) {
        this.editingScore = {
          team1: newGame.team1_goals,
          team2: newGame.team2_goals
        }
      }
    }
  },
  emits: ['close', 'save'],
  methods: {
    saveEdit() {
      this.$emit('save', {
        game_id: this.game.id,
        team1_goals: this.editingScore.team1,
        team2_goals: this.editingScore.team2
      })
    }
  }
}
</script> 