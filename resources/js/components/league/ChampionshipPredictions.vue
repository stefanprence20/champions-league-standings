<template>
  <div class="bg-white rounded-lg border border-gray-200 shadow-sm h-full flex flex-col">
    <div class="px-6 py-4 border-b border-gray-200">
      <h2 class="text-base font-semibold text-gray-800">Championship Predictions</h2>
    </div>
    <div class="flex-1 flex flex-col justify-center px-6 py-4">
      <div v-for="(team, index) in standings.slice(0, 4)" :key="team.id" class="flex items-center justify-between w-full py-3">
        <div class="flex items-center gap-4 min-w-0">
          <span class="text-sm text-gray-500 w-8 text-right">{{ index + 1 }}.</span>
          <span class="font-medium text-gray-900 text-base">{{ team.name }}</span>
        </div>
        <span class="font-bold text-blue-600 text-base">{{ team.championship_chance ? team.championship_chance.toFixed(1) : '0.0' }}%</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ChampionshipPredictions',
  props: {
    standings: {
      type: Array,
      required: true
    }
  }
}
</script> 