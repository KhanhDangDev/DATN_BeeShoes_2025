import { useState, useEffect } from "react";
import axios from "axios";
import { formatCurrencyVnd } from "../../../utils/formatCurrency";
import { FaPenToSquare } from "react-icons/fa6";
// antd
import { Input, Table, Tag, Flex, Select, Tooltip, Pagination } from "antd";
import { PlusOutlined, SearchOutlined } from "@ant-design/icons";
// routes
import { Link } from "react-router-dom";
import { DUONG_DAN_TRANG } from "../../../routes/duong-dan";
// components
import Page from "../../../components/Page";
import Container from "../../../components/Container";
import { HeaderBreadcrumbs } from "../../../components/HeaderSection";
import IconButton from "../../../components/IconButton";
import Space from "../../../components/Space";
// hooks
import useLoading from "../../../hooks/useLoading";

const { Option } = Select;

const danhSachCacTruongDuLieu = [
  {
    title: "Mã khách hàng",
    align: "center",
    render: (text, record) => {
      return (
        <>
          <span className="fw-500">{record.ma_khach_hang}</span>
        </>
      );
    },
  },
  {
    title: "Tên khách hàng",
    align: "center",
    render: (text, record) => {
      return (
        <>
          <span className="fw-500">{record.ten_khach_hang}</span>
        </>
      );
    },
  },
  {
    title: "Giới tính",
    align: "center",
    render: (text, record) => {
      return (
        <>
          <span className="fw-500">
            {record.gioi_tinh === 1 ? "Nam" : "Nữ"}
          </span>
        </>
      );
    },
  },
  {
    title: "Số điện thoại",
    align: "center",
    render: (text, record) => {
      return (
        <>
          <span className="fw-500">{record.so_dien_thoai}</span>
        </>
      );
    },
  },
  {
    title: "Email",
    align: "center",
    render: (text, record) => {
      return (
        <>
          <span className="fw-500">{record.email}</span>
        </>
      );
    },
  },
  {
    title: "Thao tác",
    align: "center",
    render: (text, record) => {
      return (
        <Tooltip title="Chỉnh sửa">
          <Link to={DUONG_DAN_TRANG.khach_hang.cap_nhat(record.id)}>
            <FaPenToSquare className="mt-8 fs-20 root-color" />
          </Link>
        </Tooltip>
      );
    },
  },
];

export default function DanhSachSanPham() {
  const { onOpenLoading, onCloseLoading } = useLoading();
  const [data, setData] = useState([]);
  const [tuKhoa, setTuKhoa] = useState("");
  const [tongSoTrang, setTongSoTrang] = useState(0);
  const [currentPage, setCurrentPage] = useState(1);
  const [gioiTinh, setGioiTinh] = useState(-1);

  useEffect(() => {
    // khai báo hàm lấy dữ liệu
    const layDuLieuTuBackEnd = async () => {
      // bật loading
      onOpenLoading();
      try {
        // gọi api từ backend
        const response = await axios.get(
          "http://127.0.0.1:8000/api/khach-hang",
          {
            // các tham số gửi về backend
            params: {
              page: currentPage,
              tuKhoa,
              gioi_tinh: gioiTinh,
            },
          }
        );

        // nếu gọi api thành công sẽ set dữ liệu
        setData(response.data.data); // set dữ liệu được trả về từ backend

        setTongSoTrang(response.data.page.totalElement); // set tổng số trang được trả về từ backend
      } catch (error) {
        console.error(error);
        // console ra lỗi
      } finally {
        onCloseLoading();
        // tắt loading
      }
    };

    // gọi hàm vừa khai báo
    layDuLieuTuBackEnd();
  }, [tuKhoa, currentPage, gioiTinh]); // hàm sẽ được gọi khi các biến này được thay đổi
  console.log("tongSoTrang", tongSoTrang);
  return (
    <>
      <Page title="Danh sách khách hàng">
        <Container>
          <HeaderBreadcrumbs
            heading="Danh sách khách hàng"
            action={
              <Link to={DUONG_DAN_TRANG.khach_hang.tao_moi}>
                <IconButton
                  type="primary"
                  name="Thêm khách hàng"
                  icon={<PlusOutlined />}
                />
              </Link>
            }
          />

          <Space
            className="mt-15 d-flex"
            title={
              <Flex gap={14} style={{ padding: "15px 0px" }}>
                   <Select
                  value={gioiTinh == -1 ? undefined : gioiTinh}
                  onChange={(value) => {
                    setCurrentPage(1);
                    setGioiTinh(value);
                  }}
                  style={{ width: WIDTH_SELECT }}
                  placeholder="Tất cả giới tính"
                >
                  <Option key={-1} value={-1}>
                    Tất cả giới tính
                  </Option>
                  <Option key={1} value={1}>
                    Nam
                  </Option>
                  <Option key={0} value={0}>
                    Nữ
                  </Option>
                </Select>
                <Input
                  addonBefore={<SearchOutlined />}
                  value={tuKhoa}
                  onChange={(e) => {
                    setCurrentPage(1);
                    setTuKhoa(e.target.value);
                  }}
                  placeholder="Tìm kiếm khách hàng..."
                />
              </Flex>
            }
          >
            <Table
              className=""
              rowKey={"id"}
              columns={danhSachCacTruongDuLieu}
              dataSource={data} // dữ liệu từ backend
              pagination={false} // tắt phân trang mặc định của table
            />

            <Pagination
              // sử dụng component phân trang
              align="end"
              current={currentPage} // trang hiện tại
              onChange={(page) => setCurrentPage(page)} // sự kiện thay đổi trang hiện tại
              total={tongSoTrang} // tổng số trang
              className="mt-20"
              pageSize={10} // kích thước trang gồm 10 phần tử (10 phần tử trên 1 trang)
              showSizeChanger={false}
            />
          </Space>
        </Container>
      </Page>
    </>
  );
}

const WIDTH_SELECT = 300;
