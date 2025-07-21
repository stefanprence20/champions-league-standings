<template>
  <div class="min-h-screen bg-gray-50 text-gray-90 font-sans">
    <div class="max-w-7xl mx-auto px-4 py-8">
      <header class="mb-8 text-center">
        <h1 class="text-2xl font-bold text-gray-800">Champions League Simulator</h1>
        <p class="text-sm text-gray-600">Week {{ currentWeek }} of {{ totalWeeks }} total weeks</p>
      </header>
      
      <div class="max-w-7xl w-full mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 justify-items-center">
          <div class="lg:col-span-2 h-96 w-full">
            <LeagueTable :standings="standings" />
          </div>
          <div class="h-96 w-full">
            <MatchResults 
              :games="games" 
              :currentWeek="currentWeek"
              @edit-game="editGame" 
            />
          </div>
          <div class="h-96 w-full">
            <ChampionshipPredictions :standings="standings" />
          </div>
        </div>
      </div>

      <ActionButtons
        class="mt-8"
        :currentWeek="currentWeek"
        :totalWeeks="totalWeeks"
        @play-week="playWeek"
        @play-all="playAllMatches"
        @reset="resetLeague"
      />
    </div>

    <GameEditModal 
      :show="showEditModal"
      :game="editingGame"
      @close="closeEditModal"
      @save="saveGameEdit"
    />

    <div v-if="isSimulating" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-700">Simulating matches...</p>
      </div>
    </div>
  </div>
</template>

<script>
import LeagueTable from './league/LeagueTable.vue'
import MatchResults from './league/MatchResults.vue'
import GameEditModal from './league/GameEditModal.vue'
import ChampionshipPredictions from './league/ChampionshipPredictions.vue'
import ActionButtons from './league/ActionButtons.vue'

export default {
  name: 'ChampionsLeagueApp',
  components: {
    LeagueTable,
    MatchResults,
    GameEditModal,
    ChampionshipPredictions,
    ActionButtons
  },
  data() {
    return {
      standings: [],
      games: [],
      currentWeek: 0,
      totalWeeks: 6,
      isSimulating: false,
      showEditModal: false,
      editingGame: null
    }
  },
  async mounted() {
    await this.loadData()
  },
  methods: {
    async loadData() {
      try {
        await Promise.all([
          this.loadStandings(),
          this.loadGames(),
          this.loadWeekInfo()
        ])
      } catch (error) {
        console.error('Error loading data:', error)
      }
    },
    
    async loadStandings() {
      const response = await fetch('/api/league/standings')
      const data = await response.json()
      this.standings = data.data
    },
    
    async loadGames() {
      const response = await fetch('/api/league/games')
      const data = await response.json()
      this.games = data.data
    },
    
    async loadWeekInfo() {
      const response = await fetch('/api/league/current-week')
      const data = await response.json()
      this.currentWeek = data.data.current_week
      this.totalWeeks = data.data.total_weeks
    },
    
    async playWeek() {
      this.isSimulating = true
      try {
        const response = await fetch('/api/league/simulate-week', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        
        if (response.ok) {
          await this.loadData()
        }
      } catch (error) {
        console.error('Error simulating week:', error)
      } finally {
        this.isSimulating = false
      }
    },
    
    async playAllMatches() {
      this.isSimulating = true
      try {
        const response = await fetch('/api/league/simulate-all', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        
        if (response.ok) {
          await this.loadData()
        }
      } catch (error) {
        console.error('Error simulating all matches:', error)
      } finally {
        this.isSimulating = false
      }
    },
    
    async resetLeague() {
      if (!confirm('Are you sure you want to reset the league? This will clear all match results.')) {
        return
      }
      
      try {
        const response = await fetch('/api/league/reset', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        
        if (response.ok) {
          await this.loadData()
        }
      } catch (error) {
        console.error('Error resetting league:', error)
      }
    },
    
    editGame(game) {
      this.editingGame = game
      this.showEditModal = true
    },
    
    async saveGameEdit(gameData) {
      try {
        const response = await fetch('/api/league/games', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(gameData)
        })
        
        if (response.ok) {
          await this.loadData()
          this.closeEditModal()
        }
      } catch (error) {
        console.error('Error updating game:', error)
      }
    },
    
    closeEditModal() {
      this.showEditModal = false
      this.editingGame = null
    }
  }
}
</script>
