<template>
  <div class="loading-container">
    <Row>
      <Col span="24">
        <Spin fix>
          <Icon type="load-c" size="30" class="spin-icon-load"></Icon>
        </Spin>
      </Col>
    </Row>
  </div>
</template>

<script>
export default {
  name: "cpnt-thumb-loading",
};
</script>

<style lang="scss" scoped>
.loading-container {
  padding: 10px;

  .spin-icon-load {
    animation: ani-spin 1s linear infinite;
  }

  @keyframes ani-spin {
    from {
      transform: rotate(0deg);
    }
    50% {
      transform: rotate(180deg);
    }
    to {
      transform: rotate(360deg);
    }
  }
}
</style>
