<template>
  <div>
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="案件码">
          <el-input size="small" v-model="searchForm.no" placeholder="请输入案件码" clearable></el-input>
        </el-form-item>
        <el-form-item label="姓名">
          <el-input size="small" v-model="searchForm.name" placeholder="请输入姓名" clearable></el-input>
        </el-form-item>
        <el-form-item label="申请时间">
          <div class="block">
            <span class="demonstration"></span>
            <el-date-picker size="small" v-model="searchForm.create_time" type="daterange" range-separator="至" start-placeholder="开始日期" end-placeholder="结束日期"></el-date-picker>
          </div>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="el-icon-search" @click="onSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--内容-->
    <div>
      <div class="table-wrap">
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="全部案件" name="all"></el-tab-pane>
          <el-tab-pane :label="'待受理(' + order_count.accepting + ')'" name="accepting"></el-tab-pane>
          <el-tab-pane :label="'调解中(' + order_count.adjusting + ')'" name="adjusting"></el-tab-pane>
          <el-tab-pane :label="'调解失败'" name="fail"></el-tab-pane>
          <el-tab-pane :label="'已调解'" name="adjusted"></el-tab-pane>
        </el-tabs>
        <el-table size="small" :data="tableData.data" :span-method="arraySpanMethod" style="width: 100%" v-loading="loading">
          <el-table-column prop="category_name" label="分类">
            <template slot-scope="scope">
              <div class="order-code" v-if="scope.row.is_top_row">
                <span class="c_main">案件码：{{ scope.row.no }}</span>
                <span class="pl16">申请时间：{{ scope.row.create_time }}</span>
              </div>
              <template v-else>
                <div class="info">
                  <div class="name gray3">
                    <span>{{ scope.row.category_name }}</span>
                  </div>
                </div>
              </template>
            </template>
          </el-table-column>
          <el-table-column prop="name" label="姓名">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              <div>{{ scope.row.name }}</div>
              <div class="gray9">用户ID：({{ scope.row.uid }})</div>
            </template>
          </el-table-column>
          <el-table-column prop="mobile" label="联系方式">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              {{ scope.row.mobile }}
            </template>
          </el-table-column>
          <el-table-column prop="appeal" label="诉求">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              {{ scope.row.appeal }}
            </template>
          </el-table-column>
          <el-table-column prop="other_name" label="对方姓名">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              {{ scope.row.other_name }}
            </template>
          </el-table-column>
          <el-table-column prop="other_phone" label="对方联系方式">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              {{ scope.row.other_phone }}
            </template>
          </el-table-column>
          <el-table-column prop="state_text" label="状态">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              {{ scope.row.state_text }}
            </template>
          </el-table-column>
          <el-table-column fixed="right" width="150px">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              <el-button @click="addClick(scope.row)" size="mini" v-auth="'/order/order/detail'">详情</el-button>
              <el-button @click="goNotice(scope.row)" size="mini" v-auth="'/order/order/notice'">消息</el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!--分页-->
      <div class="pagination">
        <el-pagination @size-change="handleSizeChange" @current-change="handleCurrentChange" background :current-page="curPage" :page-size="pageSize" layout="total, prev, pager, next, jumper" :total="totalDataNumber"></el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
  import OrderApi from '@/api/order.js';
  export default {
    data() {
      return {
        type: 'all',
        /*切换菜单*/
        activeName: 'all',
        /*是否加载完成*/
        loading: true,
        /*列表数据*/
        tableData: {},
        /*一页多少条*/
        pageSize: 20,
        /*一共多少条数据*/
        totalDataNumber: 0,
        /*当前是第几页*/
        curPage: 1,
        /*横向表单数据模型*/
        searchForm: {
          no: '',
          name: '',
          create_time: ''
        },
        /*统计*/
        order_count: {
          accepting: 0,
          adjusting: 0
        }
      };
    },
    created() {
      /*获取列表*/
      let curPage = sessionStorage.getItem('order_curPage');
      if (curPage) {
        this.curPage = parseInt(curPage);
      }
      this.getData();
    },
    methods: {
      /*跨多列*/
      arraySpanMethod(row) {
        if (row.rowIndex % 2 == 0) {
          if (row.columnIndex === 0) {
            return [1, 8];
          }
        }
      },

      /*选择第几页*/
      handleCurrentChange(val) {
        let self = this;
        self.loading = true;
        self.curPage = val;
        sessionStorage.setItem('order_curPage', val);
        self.getData();
      },

      /*每页多少条*/
      handleSizeChange(val) {
        this.curPage = 1;
        this.pageSize = val;
        sessionStorage.setItem('order_curPage', 1);
        this.getData();
      },

      /*切换菜单*/
      handleClick(tab, event) {
        let self = this;
        self.curPage = 1;
        self.loading = true;
        self.type = tab.name;
        sessionStorage.setItem('order_curPage', 1);
        self.getData();
      },

      /*获取列表*/
      getData() {
        let self = this;
        let Params = {};
        Params.type = self.type;
        Params.page = self.curPage;
        Params.list_rows = self.pageSize;
        OrderApi.orderlist(Params, true)
          .then(res => {
            self.loading = false;
            let list = [];
            for (let i = 0; i < res.data.list.data.length; i++) {
              let item = res.data.list.data[i];
              let topitem = {
                no: item.no,
                create_time: item.create_time,
                is_top_row: true
              };
              list.push(topitem);
              list.push(item);
            }

            self.tableData.data = list;
            self.totalDataNumber = res.data.list.total;
            self.order_count = res.data.order_count.order_count;
          })
          .catch(error => {
            self.loading = false;
          });
        self.$parent.$refs.rightContentBox && (self.$parent.$refs.rightContentBox.scrollTop = 0);
      },
      goNotice(row) {
        let self = this;
        self.loading = true;
        OrderApi.getNotice({
            id: row.id
          }, true)
          .then(res => {
            self.loading = false;
            this.$router.push({
              path: '/notice/notice/Index',
              query: {
                id: res.data.id
              }
            });
          })
          .catch(error => {
            self.loading = false;
          });
      },
      /*打开添加*/
      addClick(row) {
        let self = this;
        this.$router.push({
          path: '/order/order/detail',
          query: {
            id: row.id
          }
        });
      },
      /*搜索查询*/
      onSubmit() {
        let self = this;
        let Params = this.searchForm;
        Params.type = self.type;
        Params.page = self.curPage;
        Params.list_rows = self.pageSize;
        self.loading = true;
        OrderApi.orderlist(Params, true)
          .then(res => {
            self.loading = false;
            let list = [];
            for (let i = 0; i < res.data.list.data.length; i++) {
              let item = res.data.list.data[i];
              let topitem = {
                no: item.no,
                create_time: item.create_time,
                is_top_row: true
              };
              list.push(topitem);
              list.push(item);
            }
            self.tableData.data = list;
            self.totalDataNumber = res.data.list.total;
            self.order_count = res.data.order_count.order_count;
          })
          .catch(error => {
            self.loading = false;
          });
      }
    }
  };

</script>
<style lang="scss" scoped>
  /deep/ .table-wrap .el-table__body tbody .el-table__row:nth-child(odd) {
    background: #f5f7fa;
  }
</style>
