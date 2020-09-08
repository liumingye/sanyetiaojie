<template>
  <div>
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="提交时间">
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
        <el-table size="small" :data="tableData.data" :span-method="arraySpanMethod" style="width: 100%" v-loading="loading">
          <el-table-column label="用户">
            <template slot-scope="scope">
              <div class="order-code" v-if="scope.row.is_top_row">
                <span class="c_main">ID：{{ scope.row.id }}</span>
                <span class="pl16">提交时间：{{ scope.row.create_time }}</span>
              </div>
              <template v-else>
                <div class="info">
                  <div class="name gray3">
                    <span>{{ scope.row.user.nickName }}</span>
                    <div class="gray9">用户ID：({{ scope.row.uid }})</div>
                  </div>
                </div>
              </template>
            </template>
          </el-table-column>
          <el-table-column prop="mobile" label="分类">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">{{ scope.row.category_name }}</template>
          </el-table-column>
          <el-table-column prop="mobile" label="问题">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">{{ scope.row.text }}</template>
          </el-table-column>
          <el-table-column prop="state_text" label="状态">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">{{ scope.row.state_text }}</template>
          </el-table-column>
          <el-table-column fixed="right" width="150px">
            <template slot-scope="scope" v-if="!scope.row.is_top_row">
              <el-button @click="addClick(scope.row)" size="mini" v-auth="'/order/help/detail'">详情</el-button>
              <el-button @click="goNotice(scope.row)" size="mini" v-auth="'/order/help/notice'">消息</el-button>
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
import HelpApi from "@/api/help.js";
export default {
  data() {
    return {
      type: "all",
      /*切换菜单*/
      activeName: "all",
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
        no: "",
        name: "",
        create_time: "",
      },
      /*统计*/
      order_count: {
        accepting: 0,
        adjusting: 0,
      },
    };
  },
  created() {
    /*获取列表*/
    let curPage = sessionStorage.getItem("help_curPage");
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
          return [1, 2];
        }
      }
    },

    /*选择第几页*/
    handleCurrentChange(val) {
      let self = this;
      self.curPage = val;
      self.loading = true;
      sessionStorage.setItem("help_curPage", val);
      self.getData();
    },

    /*每页多少条*/
    handleSizeChange(val) {
      this.curPage = 1;
      this.pageSize = val;
      sessionStorage.setItem("help_curPage", 1);
      this.getData();
    },

    /*切换菜单*/
    handleClick(tab, event) {
      let self = this;
      self.curPage = 1;
      self.loading = true;
      self.type = tab.name;
      sessionStorage.setItem("help_curPage", 1);
      self.getData();
    },
    /*获取列表*/
    getData() {
      let self = this;
      let Params = {};
      Params.type = self.type;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      HelpApi.getlist(Params, true)
        .then((res) => {
          self.loading = false;
          let list = [];
          for (let i = 0; i < res.data.list.data.length; i++) {
            let item = res.data.list.data[i];
            let topitem = {
              id: item.id,
              create_time: item.create_time,
              is_top_row: true,
            };
            list.push(topitem);
            list.push(item);
          }

          self.tableData.data = list;
          self.totalDataNumber = res.data.list.total;
          self.order_count = res.data.order_count.order_count;
        })
        .catch((error) => {});
    },
    goNotice(row) {
      let self = this;
      self.loading = true;
      HelpApi.getNotice(
        {
          id: row.id,
        },
        true
      )
        .then((res) => {
          self.loading = false;
          this.$router.push({
            path: "/notice/help/Index",
            query: {
              id: res.data.id,
            },
          });
        })
        .catch((error) => {
          self.loading = false;
        });
    },
    /*打开添加*/
    addClick(row) {
      let self = this;
      this.$router.push({
        path: "/order/help/detail",
        query: {
          id: row.id,
        },
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
      HelpApi.getlist(Params, true)
        .then((res) => {
          self.loading = false;
          let list = [];
          for (let i = 0; i < res.data.list.data.length; i++) {
            let item = res.data.list.data[i];
            let topitem = {
              no: item.no,
              create_time: item.create_time,
              is_top_row: true,
            };
            list.push(topitem);
            list.push(item);
          }
          self.tableData.data = list;
          self.totalDataNumber = res.data.list.total;
          self.order_count = res.data.order_count.order_count;
        })
        .catch((error) => {});
    },
  },
};
</script>
<style lang="scss" scoped>
/deep/ .table-wrap .el-table__body tbody .el-table__row:nth-child(odd) {
  background: #f5f7fa;
}
</style>
