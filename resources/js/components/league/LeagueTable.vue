<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-base font-semibold text-gray-800">League Table</h2>
    </div>
    
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200">
            <th class="px-3 py-2 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Team</th>
            <th class="px-32ext-center text-xs font-medium text-gray-600 uppercase tracking-wider">PTS</th>
            <th class="px-32ext-center text-xs font-medium text-gray-600 uppercase tracking-wider">P</th>
            <th class="px-32ext-center text-xs font-medium text-gray-600 uppercase tracking-wider">W</th>
            <th class="px-32ext-center text-xs font-medium text-gray-600 uppercase tracking-wider">D</th>
            <th class="px-32ext-center text-xs font-medium text-gray-600 uppercase tracking-wider">L</th>
            <th class="px-32ext-center text-xs font-medium text-gray-600 uppercase tracking-wider">GF</th>
            <th class="px-32ext-center text-xs font-medium text-gray-600 uppercase tracking-wider">GA</th>
            <th class="px-32ext-center text-xs font-medium text-gray-600 uppercase tracking-wider">GD</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <tr 
            v-for="(team, index) in standings" 
            :key="team.id" 
            :class="[
              'hover:bg-gray-50 transition-colors',
              index === 0 ? 'bg-blue-50 border-l-4 border-blue-500' : ''
            ]"
          >
            <td class="px-3 py-3">
              <div class="flex items-center">
                <div class="flex-shrink-0 w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-xs font-medium text-gray-600 mr-2">
                  {{ index + 1 }}
                </div>
                <div>
                  <div class="font-medium text-gray-900t-sm">{{ team.name }}</div>
                  <div class="text-xs text-gray-500">Strength: {{ team.strength }}</div>
                </div>
              </div>
            </td>
            <td class="px-3y-3 text-center">
              <span class="font-bold text-blue-600text-base">{{ team.points }}</span>
            </td>
            <td class="px-33t-center text-gray-900text-sm">{{ team.wins + team.draws + team.losses }}</td>
            <td class="px-3y-3 text-center">
              <span class="text-green-600ont-medium text-sm">{{ team.wins }}</span>
            </td>
            <td class="px-3y-3 text-center">
              <span class="text-yellow-600ont-medium text-sm">{{ team.draws }}</span>
            </td>
            <td class="px-3y-3 text-center">
              <span class="text-red-600ont-medium text-sm">{{ team.losses }}</span>
            </td>
            <td class="px-33t-center text-gray-900ext-sm">{{ team.goals_for }}</td>
            <td class="px-33t-center text-gray-900ext-sm">{{ team.goals_against }}</td>
            <td class="px-3y-3 text-center">
              <span :class="getGoalDifferenceClass(team.goals_for - team.goals_against)" class="text-sm">
                {{ team.goals_for - team.goals_against > 0 ? '+' : '' }}{{ team.goals_for - team.goals_against }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'LeagueTable',
  props: {
    standings: {
      type: Array,
      required: true
    }
  },
  methods: {
    getGoalDifferenceClass(gd) {
      if (gd > 0) return 'text-green-600 font-medium'
      if (gd < 0) return 'text-red-600 font-medium'
      return 'text-gray-50'
    }
  }
}
</script> 