<template>
  <div class="how-it-works min-h-screen bg-gradient-to-br from-secondary-50 via-white to-primary-50 dark:from-secondary-950 dark:via-secondary-900 dark:to-secondary-900">
    <!-- Hero -->
    <section class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-primary-700 via-primary-600 to-secondary-700">
        <div class="absolute inset-0 bg-black/25"></div>
        <div class="absolute -top-24 -left-16 w-80 h-80 bg-white/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-1/3 -right-20 w-96 h-96 bg-primary-300/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
      </div>

      <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-20 text-center">
        <p class="text-4xl sm:text-5xl md:text-6xl font-bold text-white tracking-tight mb-6">
          CarWise<span class="text-primary-200">.ai</span>
        </p>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold text-white/95 mb-5 max-w-3xl mx-auto leading-snug">
          From first symptom to the right fix — in one guided flow
        </h1>
        <p class="text-lg text-white/75 max-w-2xl mx-auto mb-10 leading-relaxed">
          See exactly how owners use CarWise to diagnose issues, find parts, and keep every vehicle on track.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <a
            href="#journey"
            class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl bg-white text-primary-700 font-semibold shadow-xl hover:bg-primary-50 transition-all duration-300"
          >
            Walk through the journey
          </a>
          <router-link
            to="/register"
            class="inline-flex items-center justify-center px-7 py-3.5 rounded-2xl border border-white/30 bg-white/10 backdrop-blur-sm text-white font-semibold hover:bg-white/20 transition-all duration-300"
          >
            Start free
          </router-link>
        </div>
      </div>
    </section>

    <!-- Promise strip -->
    <section class="relative -mt-8 z-10 px-4">
      <div class="max-w-5xl mx-auto grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-0 sm:divide-x divide-secondary-200/60 dark:divide-secondary-700/60 bg-white/90 dark:bg-secondary-800/90 backdrop-blur-xl rounded-2xl border border-white/40 dark:border-secondary-700/50 shadow-soft overflow-hidden">
        <div
          v-for="item in promises"
          :key="item.title"
          class="px-6 py-5 text-center"
        >
          <p class="text-sm font-semibold text-secondary-900 dark:text-white">{{ item.title }}</p>
          <p class="mt-1 text-sm text-secondary-500 dark:text-secondary-400">{{ item.detail }}</p>
        </div>
      </div>
    </section>

    <!-- Journey -->
    <section id="journey" class="py-20 sm:py-24 scroll-mt-20">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-14">
          <p class="text-sm font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400 mb-3">
            The journey
          </p>
          <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 dark:text-white mb-4">
            Six clear steps from “something’s wrong” to “fixed”
          </h2>
          <p class="text-lg text-secondary-600 dark:text-secondary-300 leading-relaxed">
            CarWise is built around the real sequence owners already follow — we just make each step faster and clearer.
          </p>
        </div>

        <ol class="space-y-0">
          <li
            v-for="(step, index) in journey"
            :key="step.title"
            class="group relative grid grid-cols-[auto_1fr] gap-5 sm:gap-8 pb-12 last:pb-0"
          >
            <div class="flex flex-col items-center">
              <div
                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 text-white font-bold flex items-center justify-center shadow-md ring-4 ring-white dark:ring-secondary-900"
              >
                {{ index + 1 }}
              </div>
              <div
                v-if="index < journey.length - 1"
                class="w-px flex-1 min-h-[2.5rem] bg-gradient-to-b from-primary-300 to-transparent dark:from-primary-700 mt-2"
              ></div>
            </div>
            <div class="pt-1 pb-2">
              <h3 class="text-xl font-bold text-secondary-900 dark:text-white mb-2">
                {{ step.title }}
              </h3>
              <p class="text-secondary-600 dark:text-secondary-300 leading-relaxed mb-3">
                {{ step.body }}
              </p>
              <p class="text-sm text-primary-700 dark:text-primary-300 font-medium">
                {{ step.outcome }}
              </p>
            </div>
          </li>
        </ol>
      </div>
    </section>

    <!-- Methods -->
    <section class="py-20 bg-secondary-900 text-white">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-12">
          <p class="text-sm font-semibold uppercase tracking-wider text-primary-300 mb-3">
            Methods
          </p>
          <h2 class="text-3xl sm:text-4xl font-bold mb-4">
            Tell CarWise what’s happening — your way
          </h2>
          <p class="text-lg text-secondary-300 leading-relaxed">
            No special hardware required. Start with whatever evidence you already have.
          </p>
        </div>

        <div class="grid sm:grid-cols-2 gap-8">
          <div
            v-for="method in methods"
            :key="method.title"
            class="border-t border-white/15 pt-6"
          >
            <h3 class="text-lg font-semibold text-white mb-2">{{ method.title }}</h3>
            <p class="text-secondary-300 leading-relaxed">{{ method.body }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Benefits -->
    <section class="py-20 sm:py-24">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-12">
          <p class="text-sm font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400 mb-3">
            What you gain
          </p>
          <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 dark:text-white mb-4">
            Less guesswork. Less wasted time. Clear next moves.
          </h2>
        </div>

        <ul class="grid sm:grid-cols-2 gap-x-10 gap-y-8">
          <li
            v-for="benefit in benefits"
            :key="benefit.title"
            class="flex gap-4"
          >
            <span class="mt-1.5 h-2.5 w-2.5 rounded-full bg-primary-500 shrink-0"></span>
            <div>
              <h3 class="font-semibold text-secondary-900 dark:text-white mb-1">{{ benefit.title }}</h3>
              <p class="text-secondary-600 dark:text-secondary-300 leading-relaxed">{{ benefit.body }}</p>
            </div>
          </li>
        </ul>
      </div>
    </section>

    <!-- Convenience -->
    <section class="py-20 bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-secondary-800/50 dark:to-secondary-900/80">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-12">
          <p class="text-sm font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400 mb-3">
            Built for everyday owners
          </p>
          <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 dark:text-white mb-4">
            Convenience that compounds over every vehicle you add
          </h2>
        </div>

        <div class="space-y-8">
          <article
            v-for="item in conveniences"
            :key="item.title"
            class="sm:grid sm:grid-cols-[11rem_1fr] gap-6 items-start border-b border-secondary-200/80 dark:border-secondary-700/60 pb-8 last:border-0 last:pb-0"
          >
            <h3 class="text-base font-bold text-primary-700 dark:text-primary-300 mb-2 sm:mb-0">
              {{ item.title }}
            </h3>
            <p class="text-secondary-600 dark:text-secondary-300 leading-relaxed">
              {{ item.body }}
            </p>
          </article>
        </div>
      </div>
    </section>

    <!-- Who it's for -->
    <section class="py-20 sm:py-24">
      <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mb-12">
          <p class="text-sm font-semibold uppercase tracking-wider text-primary-600 dark:text-primary-400 mb-3">
            Who it’s for
          </p>
          <h2 class="text-3xl sm:text-4xl font-bold text-secondary-900 dark:text-white">
            Designed around how people actually care for cars
          </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-10">
          <div
            v-for="persona in personas"
            :key="persona.title"
          >
            <h3 class="text-lg font-bold text-secondary-900 dark:text-white mb-2">{{ persona.title }}</h3>
            <p class="text-secondary-600 dark:text-secondary-300 leading-relaxed">{{ persona.body }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Closing CTA -->
    <section class="relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-r from-primary-700 via-primary-600 to-secondary-700"></div>
      <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">
          Ready to try the flow on your car?
        </h2>
        <p class="text-lg text-white/80 mb-10 max-w-2xl mx-auto leading-relaxed">
          Create an account, add a vehicle, and run your first AI diagnosis in minutes.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <router-link
            to="/register"
            class="inline-flex items-center justify-center px-8 py-3.5 rounded-2xl bg-white text-primary-700 font-semibold shadow-xl hover:bg-primary-50 transition-all duration-300"
          >
            Create free account
          </router-link>
          <router-link
            to="/diagnose"
            class="inline-flex items-center justify-center px-8 py-3.5 rounded-2xl border border-white/30 bg-white/10 text-white font-semibold hover:bg-white/20 transition-all duration-300"
          >
            Open AI Diagnosis
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const promises = [
  {
    title: 'Diagnose first',
    detail: 'Understand the issue before you spend',
  },
  {
    title: 'Act with clarity',
    detail: 'Parts, shops, and next steps in one place',
  },
  {
    title: 'Stay ahead',
    detail: 'History and reminders for every car you own',
  },
]

const journey = [
  {
    title: 'Create your CarWise account',
    body: 'Sign up in moments. Your dashboard becomes the home for vehicles, diagnoses, and parts recommendations.',
    outcome: 'Outcome: a private workspace for every car you manage.',
  },
  {
    title: 'Add the vehicle that needs attention',
    body: 'Save make, model, year, and details so AI and parts matching stay accurate across visits — not just one session.',
    outcome: 'Outcome: context that follows you from diagnosis to repair.',
  },
  {
    title: 'Describe the problem (photos, video, or words)',
    body: 'Share symptoms, noises, warning lights, or media. CarWise is built for real-world evidence, not only OBD jargon.',
    outcome: 'Outcome: the AI gets enough signal to focus on likely causes.',
  },
  {
    title: 'Get an AI diagnosis with clear next steps',
    body: 'Receive a structured report: probable issues, what to check, and practical guidance so you’re not guessing in the driveway.',
    outcome: 'Outcome: confidence before you visit a workshop or order parts.',
  },
  {
    title: 'Find the right parts — or continue to a seller',
    body: 'Use the marketplace for compatible parts, or explore live listings when you’re shopping for a vehicle. Outbound links are tracked securely when you continue to a seller site.',
    outcome: 'Outcome: fewer wrong purchases and fewer dead ends.',
  },
  {
    title: 'Track maintenance and return anytime',
    body: 'Keep history, reminders, and past diagnoses together so the next issue starts from known ground — not from zero.',
    outcome: 'Outcome: long-term care that gets smarter with every visit.',
  },
]

const methods = [
  {
    title: 'Photos & video',
    body: 'Capture leaks, wear, dashboard lights, or damage. Visual context helps the AI narrow the problem space.',
  },
  {
    title: 'Plain-language symptoms',
    body: 'Describe what you hear, smell, or feel — “rattles when cold,” “pulls left when braking” — without mechanic vocabulary.',
  },
  {
    title: 'Vehicle profile',
    body: 'Year, make, and model sharpen recommendations so advice and parts fit the car you actually drive.',
  },
  {
    title: 'Marketplace & live inventory',
    body: 'After diagnosis, move into parts search — or browse real market listings when you’re looking for your next vehicle.',
  },
]

const benefits = [
  {
    title: 'Faster clarity',
    body: 'Skip random forum threads. Get a structured read of what’s likely wrong and what to do next.',
  },
  {
    title: 'Smarter spending',
    body: 'Know which parts or services matter before you authorize expensive work.',
  },
  {
    title: 'One place for every car',
    body: 'Owners with multiple vehicles keep profiles, history, and reminders under the same login.',
  },
  {
    title: 'Always available',
    body: 'Start a check at night, on the roadside, or before a dealer appointment — AI support doesn’t wait for shop hours.',
  },
  {
    title: 'From insight to action',
    body: 'Diagnosis connects naturally into parts and listing flows instead of dumping you into a PDF and walking away.',
  },
  {
    title: 'Privacy-minded handoffs',
    body: 'When you continue to an external seller, CarWise uses secure redirects and keeps click logging minimal.',
  },
]

const conveniences = [
  {
    title: 'Mobile-ready',
    body: 'Use CarWise on phone or desktop. Capture evidence where the car is — then finish the report wherever you are.',
  },
  {
    title: 'Guided UX',
    body: 'Dashboards, diagnosis, My Cars, and Parts Store follow the same product logic so you never relearn the app.',
  },
  {
    title: 'Plans that scale',
    body: 'Start free, then unlock more capacity through subscription plans when diagnosis volume or fleet needs grow.',
  },
  {
    title: 'Support when stuck',
    body: 'Live chat and help paths stay available so a confusing symptom doesn’t leave you stranded mid-flow.',
  },
]

const personas = [
  {
    title: 'Everyday drivers',
    body: 'When a new noise appears, you want a calm first answer — not a blank invoice.',
  },
  {
    title: 'Multi-car households',
    body: 'Keep each vehicle’s history separate but accessible under one account.',
  },
  {
    title: 'DIY & shop-bound owners',
    body: 'Arrive prepared: whether you wrench yourself or brief a mechanic with a clearer starting point.',
  },
]
</script>

<style scoped>
.how-it-works {
  scroll-behavior: smooth;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-18px);
  }
}

.animate-float {
  animation: float 7s ease-in-out infinite;
}

@media (prefers-reduced-motion: reduce) {
  .animate-float {
    animation: none;
  }

  .how-it-works {
    scroll-behavior: auto;
  }
}
</style>
