<template>
  <Teleport to="body">
    <Transition name="cw-lightbox">
      <div
        v-if="open"
        class="cw-lightbox"
        role="dialog"
        aria-modal="true"
        :aria-label="title || 'Problem visual'"
        @click.self="close"
      >
        <div class="cw-lightbox__bar">
          <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-white">{{ title }}</p>
            <p v-if="caption" class="truncate text-xs text-white/55">{{ caption }}</p>
          </div>
          <div class="flex items-center gap-2">
            <button
              v-if="hasGallery"
              type="button"
              class="cw-lightbox__icon-btn"
              :disabled="!canPrev"
              aria-label="Previous"
              @click="prev"
            >
              ‹
            </button>
            <button
              v-if="hasGallery"
              type="button"
              class="cw-lightbox__icon-btn"
              :disabled="!canNext"
              aria-label="Next"
              @click="next"
            >
              ›
            </button>
            <button type="button" class="cw-lightbox__icon-btn" aria-label="Close" @click="close">✕</button>
          </div>
        </div>

        <div class="cw-lightbox__stage" @click.self="close">
          <div class="cw-lightbox__frame">
            <img
              :src="src"
              :alt="title || 'Diagnosis visual'"
              class="cw-lightbox__img"
              draggable="false"
            >

            <!-- Red problem-zone annotation overlay -->
            <div
              class="cw-hotspot"
              :style="hotspotStyle"
              aria-hidden="true"
            >
              <span class="cw-hotspot__pulse" />
              <span class="cw-hotspot__box" />
              <span class="cw-hotspot__corners" />
            </div>

            <div class="cw-callout" :style="calloutStyle">
              <div class="cw-callout__tag">Problem zone</div>
              <p class="cw-callout__title">{{ annotationLabel }}</p>
              <p class="cw-callout__body">{{ annotationMeaning }}</p>
            </div>
          </div>
        </div>

        <p class="cw-lightbox__hint">Tap outside or press Esc to close · red mark = fault area to inspect</p>
      </div>
    </Transition>
  </Teleport>
</template>

<script>
import { computed, onMounted, onBeforeUnmount, watch } from 'vue'

const DEFAULT_HOTSPOT = { x: 42, y: 34, w: 28, h: 30 }

export default {
  name: 'DiagnosisImageLightbox',
  props: {
    open: { type: Boolean, default: false },
    src: { type: String, default: '' },
    title: { type: String, default: '' },
    caption: { type: String, default: '' },
    annotation: { type: Object, default: null },
    gallery: { type: Array, default: () => [] },
    index: { type: Number, default: 0 },
  },
  emits: ['close', 'update:index'],
  setup(props, { emit }) {
    const hasGallery = computed(() => Array.isArray(props.gallery) && props.gallery.length > 1)
    const canPrev = computed(() => props.index > 0)
    const canNext = computed(() => props.index < (props.gallery?.length || 0) - 1)

    const hotspot = computed(() => {
      const h = props.annotation?.hotspot
      if (h && typeof h.x === 'number') return h
      return DEFAULT_HOTSPOT
    })

    const hotspotStyle = computed(() => ({
      left: `${hotspot.value.x}%`,
      top: `${hotspot.value.y}%`,
      width: `${hotspot.value.w}%`,
      height: `${hotspot.value.h}%`,
    }))

    const calloutStyle = computed(() => {
      const h = hotspot.value
      const preferLeft = h.x > 50
      return {
        left: preferLeft ? '4%' : 'auto',
        right: preferLeft ? 'auto' : '4%',
        bottom: '4%',
        maxWidth: 'min(22rem, 86%)',
      }
    })

    const annotationLabel = computed(
      () => props.annotation?.label || props.title || 'Fault area'
    )
    const annotationMeaning = computed(
      () => props.annotation?.meaning
        || props.caption
        || 'Inspect this marked zone before replacing unrelated parts.'
    )

    const close = () => emit('close')
    const prev = () => {
      if (canPrev.value) emit('update:index', props.index - 1)
    }
    const next = () => {
      if (canNext.value) emit('update:index', props.index + 1)
    }

    const onKey = (e) => {
      if (!props.open) return
      if (e.key === 'Escape') close()
      if (e.key === 'ArrowLeft') prev()
      if (e.key === 'ArrowRight') next()
    }

    watch(
      () => props.open,
      (isOpen) => {
        document.body.style.overflow = isOpen ? 'hidden' : ''
      }
    )

    onMounted(() => window.addEventListener('keydown', onKey))
    onBeforeUnmount(() => {
      window.removeEventListener('keydown', onKey)
      document.body.style.overflow = ''
    })

    return {
      hasGallery,
      canPrev,
      canNext,
      hotspotStyle,
      calloutStyle,
      annotationLabel,
      annotationMeaning,
      close,
      prev,
      next,
    }
  },
}
</script>

<style scoped>
.cw-lightbox {
  position: fixed;
  inset: 0;
  z-index: 80;
  display: flex;
  flex-direction: column;
  background: rgba(2, 6, 23, 0.94);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  padding: env(safe-area-inset-top, 0) env(safe-area-inset-right, 0) env(safe-area-inset-bottom, 0) env(safe-area-inset-left, 0);
}

.cw-lightbox__bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.85rem 1rem;
}

.cw-lightbox__icon-btn {
  display: inline-flex;
  height: 2.25rem;
  min-width: 2.25rem;
  align-items: center;
  justify-content: center;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.14);
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  font-size: 1.1rem;
  line-height: 1;
}
.cw-lightbox__icon-btn:disabled {
  opacity: 0.35;
}

.cw-lightbox__stage {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem 1rem;
  min-height: 0;
}

.cw-lightbox__frame {
  position: relative;
  width: min(960px, 100%);
  max-height: calc(100vh - 7rem);
  border-radius: 1.1rem;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: #020617;
  box-shadow: 0 30px 80px rgba(0, 0, 0, 0.45);
}

.cw-lightbox__img {
  display: block;
  width: 100%;
  max-height: calc(100vh - 7rem);
  object-fit: contain;
  user-select: none;
}

.cw-hotspot {
  position: absolute;
  pointer-events: none;
  z-index: 2;
}

.cw-hotspot__box {
  position: absolute;
  inset: 0;
  border: 2px solid rgba(239, 68, 68, 0.95);
  border-radius: 0.65rem;
  box-shadow:
    0 0 0 1px rgba(127, 29, 29, 0.55),
    0 0 28px rgba(239, 68, 68, 0.35);
}

.cw-hotspot__pulse {
  position: absolute;
  left: 50%;
  top: 50%;
  width: 1.1rem;
  height: 1.1rem;
  margin: -0.55rem 0 0 -0.55rem;
  border-radius: 999px;
  background: #ef4444;
  box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.55);
  animation: cw-pulse 1.8s ease-out infinite;
}

.cw-hotspot__corners::before,
.cw-hotspot__corners::after {
  content: '';
  position: absolute;
  width: 1.1rem;
  height: 1.1rem;
  border: 2.5px solid #f87171;
}
.cw-hotspot__corners::before {
  left: -3px;
  top: -3px;
  border-right: 0;
  border-bottom: 0;
}
.cw-hotspot__corners::after {
  right: -3px;
  bottom: -3px;
  border-left: 0;
  border-top: 0;
}

.cw-callout {
  position: absolute;
  z-index: 3;
  padding: 0.85rem 1rem;
  border-radius: 0.9rem;
  background: rgba(15, 23, 42, 0.88);
  border: 1px solid rgba(248, 113, 113, 0.55);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.4);
  color: #fff;
  backdrop-filter: blur(10px);
}

.cw-callout__tag {
  display: inline-block;
  margin-bottom: 0.35rem;
  padding: 0.15rem 0.45rem;
  border-radius: 999px;
  background: rgba(239, 68, 68, 0.18);
  color: #fecaca;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
}

.cw-callout__title {
  font-size: 0.95rem;
  font-weight: 650;
  color: #fff;
}

.cw-callout__body {
  margin-top: 0.35rem;
  font-size: 0.82rem;
  line-height: 1.45;
  color: rgba(248, 250, 252, 0.78);
}

.cw-lightbox__hint {
  text-align: center;
  padding: 0 1rem 0.85rem;
  font-size: 0.7rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.4);
}

@keyframes cw-pulse {
  0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.55); }
  70% { box-shadow: 0 0 0 16px rgba(239, 68, 68, 0); }
  100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}

.cw-lightbox-enter-active,
.cw-lightbox-leave-active {
  transition: opacity 0.22s ease;
}
.cw-lightbox-enter-from,
.cw-lightbox-leave-to {
  opacity: 0;
}
</style>
