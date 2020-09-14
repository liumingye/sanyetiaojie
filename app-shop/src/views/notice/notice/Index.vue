<template>
  <div v-loading="loading">
    <!--搜索表单-->
    <div class="common-seach-wrap">
      <el-form size="small" :inline="true" :model="searchForm" class="demo-form-inline">
        <el-form-item label="消息ID">
          <el-input size="small" v-model="searchForm.id" clearable></el-input>
        </el-form-item>
        <el-form-item label="用户ID">
          <el-input size="small" v-model="searchForm.uid" clearable></el-input>
        </el-form-item>
        <el-form-item>
          <el-button size="small" type="primary" icon="el-icon-search" @click="onSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </div>
    <!--内容-->
    <div class="product-content">
      <div class="table-wrap">
        <el-table size="small" :data="tableData.data" style="width: 100%">
          <el-table-column prop="id" label="ID" width="100"></el-table-column>
          <el-table-column prop="name" label="用户">
            <template slot-scope="scope">
              <div>{{ scope.row.nickName }}</div>
              <div class="gray9">用户ID：({{ scope.row.uid }})</div>
            </template>
          </el-table-column>
          <el-table-column prop="mobile" label="消息名称">
            <template slot-scope="scope">{{ scope.row.name }}</template>
          </el-table-column>
          <el-table-column prop="appeal" label="最新消息">
            <template slot-scope="scope" v-if="scope.row.msg.length > 0">{{ scope.row.msg[0].text }}</template>
          </el-table-column>
          <el-table-column fixed="right" width="100px">
            <template slot-scope="scope">
              <el-badge class="mark" :value="scope.row.unread" v-if="scope.row.unread > 0" :max="99">
                <el-button @click="addClick(scope.row)" size="mini" v-auth="'/notice/notice/detail'">回复</el-button>
              </el-badge>
              <el-button @click="addClick(scope.row)" size="mini" v-else v-auth="'/notice/notice/detail'">回复</el-button>
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
import NoticeApi from "@/api/notice.js";
export default {
  data() {
    return {
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
        id: "",
        uid: "",
        type: "mediate",
      },
    };
  },
  created() {
    /*获取列表*/
    this.searchForm.id = this.$route.query.id;
    this.searchForm.uid = this.$route.query.uid;
    let curPage = sessionStorage.getItem("notice_notice_curPage");
    if (curPage) {
      this.curPage = parseInt(curPage);
    }
    this.getData();
  },
  methods: {
    /*选择第几页*/
    handleCurrentChange(val) {
      let self = this;
      self.loading = true;
      self.curPage = val;
      sessionStorage.setItem("notice_notice_curPage", val);
      self.getData();
    },

    /*每页多少条*/
    handleSizeChange(val) {
      this.curPage = 1;
      this.pageSize = val;
      sessionStorage.setItem("notice_notice_curPage", 1);
      this.getData();
    },

    /*切换菜单*/
    handleClick(tab, event) {
      let self = this;
      self.curPage = 1;
      self.loading = true;
      self.type = tab.name;
      sessionStorage.setItem("notice_notice_curPage", 1);
      self.getData();
    },
    /*获取列表*/
    getData() {
      let self = this;
      let Params = this.searchForm;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      NoticeApi.noticeList(Params, true)
        .then((res) => {
          self.loading = false;
          let list = [];
          for (let i = 0; i < res.data.list.data.length; i++) {
            let item = res.data.list.data[i];
            list.push(item);
          }

          self.tableData.data = list;
          self.totalDataNumber = res.data.list.total;
          self.order_count = res.data.order_count.order_count;
        })
        .catch((error) => {
          self.loading = false;
        });
      self.$parent.$refs.rightContentBox &&
        (self.$parent.$refs.rightContentBox.scrollTop = 0);
    },
    /*打开添加*/
    addClick(row) {
      this.$router.push({
        path: "/notice/notice/detail",
        query: {
          title: row.name,
          id: row.id,
        },
      });
    },
    /*搜索查询*/
    onSubmit() {
      let self = this;
      let Params = this.searchForm;
      Params.page = self.curPage;
      Params.list_rows = self.pageSize;
      self.loading = true;
      NoticeApi.noticeList(Params, true)
        .then((res) => {
          self.loading = false;
          let list = [];
          for (let i = 0; i < res.data.list.data.length; i++) {
            let item = res.data.list.data[i];
            list.push(item);
          }

          self.tableData.data = list;
          self.totalDataNumber = res.data.list.total;
          self.order_count = res.data.order_count.order_count;
        })
        .catch((error) => {
          self.loading = false;
        });
      self.$parent.$refs.rightContentBox &&
        (self.$parent.$refs.rightContentBox.scrollTop = 0);
    },
  },
};
</script>

<style lang="scss" scoped>
/deep/ .el-table .cell {
  overflow: visible;
}
</style>
