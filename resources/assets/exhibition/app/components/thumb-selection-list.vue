<!--
    Component: thumb-selection-list
    Description: A selection list with options
    Author: Nuttaphat (Logan)
    Note:
        - Mobile, HTML select is rendered
        - Desktop, iView select is rendered
    The reason why we separate conditional renders is that
    on mobile iView select cannot be scrolled for an unknown reason
-->
<template>
  <div class="selection-list-container">
    <!-- Mobile -->
    <select
      class="selection-list-panel"
      v-if="isMobile"
      :value="value"
      :disabled="options.length < 1"
      @change="updateSelection($event.target.value)"
    >
      <option
        v-for="option in options"
        :key="getOptionKey(option)"
        :value="getOptionKey(option)"
      >
        {{ getOptionLabel(option) }}
      </option>
    </select>
    <!-- Desktop (IView) -->
    <Select
      v-else
      :value="value"
      :disabled="options.length < 1"
      @on-change="updateSelection($event)"
    >
      <Option
        v-for="option in options"
        :key="getOptionKey(option)"
        :value="getOptionKey(option)"
      >
        {{ getOptionLabel(option) }}
      </Option>
    </Select>
  </div>
</template>

<script>
export default {
  name: "cpnt-thumb-selection-list",
  props: {
    value: {
      required: true,
    },
    options: {
      type: Array,
      required: true,
    },
    optionValueKey: {
      type: String,
      required: false,
      default: null,
    },
    optionLabelKey: {
      type: String,
      required: false,
      default: null,
    },
    isMobile: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    getOptionKey(optionData) {
      // Use optionData directly if it is not an object
      if (!optionData || !optionData.hasOwnProperty(this.optionValueKey))
        return optionData;
      return optionData[this.optionValueKey];
    },
    getOptionLabel(optionData) {
      // Use optionData directly if it is not an object
      if (!optionData || !optionData.hasOwnProperty(this.optionLabelKey))
        return optionData;
      return optionData[this.optionLabelKey];
    },
    updateSelection(val) {
      this.$emit("input", val);
    },
  },
};
</script>

<style lang="scss" scoped>
.selection-list-container {
  // Styling to imitate IView
  .selection-list-panel {
    display: block;
    box-sizing: border-box;
    height: 30px;
    width: 100%;
    padding-left: 8px;
    padding-right: 24px;

    position: relative;
    background-color: #fff;
    border-radius: 4px;
    border: 1px solid #dddee1;

    line-height: 30px;
    font-size: 12px;
    text-overflow: ellipsis;
    white-space: nowrap;

    transition: all 0.2s ease-in-out;
  }
}
</style>