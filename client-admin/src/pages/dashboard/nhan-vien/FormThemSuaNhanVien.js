import { useState, useEffect } from "react";
import * as Yup from "yup";
import axios from "axios";
import { useNavigate } from "react-router-dom";
import { formatCurrencyVnd, formatNumber } from "../../../utils/formatCurrency";
// form
import { yupResolver } from "@hookform/resolvers/yup";
import { useForm, Controller } from "react-hook-form";
// antd
import { Col, Tag, Select, Row, Button, Space, Input, DatePicker } from "antd";
import moment from "moment";
// routes
import { DUONG_DAN_TRANG } from "../../../routes/duong-dan";
// components
import FormProvider from "../../../components/hook-form/FormProvider";
import RHFInput from "../../../components/hook-form/RHFInput";
// hooks
import useConfirm from "../../../hooks/useConfirm";
import useNotification from "../../../hooks/useNotification";
import useLoading from "../../../hooks/useLoading";
import dayjs from "dayjs";

const { Option } = Select;

const DANH_SACH_TRANG_THAI_KHACH_HANG = ["Đang hoạt động", "Ngừng hoạt động"];
const DANH_SACH_GIOI_TINH_KHACH_HANG = ["Nam", "Nữ"];

// ----------------------------------------------------------------------

export default function FormThemSuaNhanVien({ laCapNhat, nhanVienHienTai }) {
  const { onOpenSuccessNotify, onOpenErrorNotify } = useNotification(); //mở thông báo
  const { showConfirm } = useConfirm(); // mở confirm
  const { onOpenLoading, onCloseLoading } = useLoading(); //mở, tắt loading

  const navigate = useNavigate();

  //   useEffect(() => {
  //     // khai báo hàm lấy dữ liệu thuộc tính nhân viên
  //     const layDuLieuThuocTinhTuBackEnd = async () => {
  //       // bật loading
  //       onOpenLoading();
  //       try {
  //         // gọi api từ backend
  //         const response = await axios.get("http://127.0.0.1:8000/api/danh-sach-thuoc-tinh");

  //         // nếu gọi api thành công sẽ set dữ liệu
  //         setListMauSac(response.data.data.listMauSac); // set dữ liệu được trả về từ backend
  //         setListThuongHieu(response.data.data.listThuongHieu); // set dữ liệu được trả về từ backend
  //       } catch (error) {
  //         console.error(error);
  //         // console ra lỗi
  //       } finally {
  //         onCloseLoading();
  //         // tắt loading
  //       }
  //     }

  //     layDuLieuThuocTinhTuBackEnd();
  //   }, [])

  // validate
  //   const NhanVienSchema = Yup.object().shape({
  // //     tenSanPham: Yup.string().trim().required('Tên không được bỏ trống'),
  // //     maSanPham: Yup.string().trim().required('Mã không được bỏ trống'),
  // //     donGia: Yup.string().required('Đơn giá không được bỏ trống'),
  // //     idMauSac: Yup.string().required('Màu không được bỏ trống'),
  // //     idThuongHieu: Yup.string().required('Thương hiệu không được bỏ trống'),
  //   });

  const NhanVienSchema = Yup.object().shape({
    ma_nhan_vien: Yup.string().required("Thông tin không được để trống"),
    ten_nhan_vien: Yup.string().required("Thông tin không được để trống"),
    email: Yup.string()
      .email("Email không hợp lệ")
      .required("Thông tin không được để trống"),
    so_dien_thoai: Yup.string().required("Thông tin không được để trống"),
    gioi_tinh: Yup.string().required("Thông tin không được để trống"),
    ngay_sinh: Yup.string()
      .required("Thông tin không được để trống")
      .test("is-valid-date", "Thông tin không được để trống", (value) => {
        console.log("value", value)
        return dayjs(value, "DD/MM/YYYY", true).isValid();
      }),
    mat_khau: Yup.string()
      .required("Thông tin không được để trống")
      .min(6, "Mật khẩu phải có ít nhất 6 ký tự"),
    trang_thai: Yup.string().required("Thông tin không được để trống"),
  });

  // giá trị mặc định của biến, tương tự useState
  const defaultValues = {
    ma_nhan_vien: nhanVienHienTai?.ma_nhan_vien || "",
    ten_nhan_vien: nhanVienHienTai?.ten_nhan_vien || "",
    ngay_sinh:
      dayjs(nhanVienHienTai?.ngay_sinh, "YYYY-MM-DD").format("DD/MM/YYYY") ||
      "",
    so_dien_thoai: nhanVienHienTai?.so_dien_thoai || "",
    mat_khau: nhanVienHienTai?.mat_khau || "",
    email: nhanVienHienTai?.email || "",
    gioi_tinh: chuyenDoiEnumThanhGioiTinh(nhanVienHienTai?.gioi_tinh),
    trang_thai: chuyenDoiEnumThanhTrangThai(nhanVienHienTai?.trang_thai),
  };

  // lấy methods từ use form
  const methods = useForm({
    resolver: yupResolver(NhanVienSchema),
    defaultValues,
  });

  // các phương thức của methods
  const { reset, control, handleSubmit } = methods;

  useEffect(() => {
    // nếu là trang cập nhật => sẽ reset lại các biến trong defaultValues
    if (laCapNhat && nhanVienHienTai) {
      reset(defaultValues);
    }
    // nếu là trang thêm mới => sẽ reset lại các biến trong defaultValues
    if (!laCapNhat) {
      reset(defaultValues);
    }
  }, [laCapNhat, nhanVienHienTai]); // gọi useEffect này mỗi khi các tham số truyền vào thay đỏi

  // hàm gọi api thêm mới nhân viên
  const post = async (body) => {
    try {
      const response = await axios.post(
        "http://127.0.0.1:8000/api/add-nhan-vien",
        body
      ); // gọi api
      navigate(DUONG_DAN_TRANG.nhan_vien.danh_sach); // chuyển sang trang cập nhật
      onOpenSuccessNotify("Thêm mới nhân viên thành công!"); // hiển thị thông báo
    } catch (error) {
      if (
        error.response &&
        error.response.data &&
        error.response.data.message
      ) {
        onOpenErrorNotify(error.response.data.message); // hiển thị thông báo
        console.log("Error message:", error.response.data.message);
      } else {
        console.log("Error:", error);
      }
    }
  };

  const put = async (body, id) => {
    try {
      const response = await axios.put(
        `http://127.0.0.1:8000/api/update-nhan-vien`,
        body
      ); // gọi API cập nhật
      navigate(DUONG_DAN_TRANG.nhan_vien.danh_sach); // chuyển sang trang cập nhật
      onOpenSuccessNotify("Cập nhật nhân viên thành công!"); // hiển thị thông báo
    } catch (error) {
      if (
        error.response &&
        error.response.data &&
        error.response.data.message
      ) {
        onOpenErrorNotify(error.response.data.message); // hiển thị thông báo
        console.log("Error message:", error.response.data.message);
      } else {
        console.log("Error:", error);
      }
    }
  };

  const onSubmit = async (data) => {
    if (!laCapNhat) {
      const body = {
        ...data, // giữ các biến cũ trong data
        trang_thai: chuyenDoiThanhEnum(data.trang_thai), // ghi đè thuộc tính trạng thái trong data, convert thành enum
        gioi_tinh: chuyenDoiThanhEnumGioiTinh(data.gioi_tinh),
        ngay_sinh: moment(data.ngay_sinh, "DD/MM/YYYY").format("YYYY-MM-DD"), // Chuyển đổi định dạng ngày tháng
      };
      console.log(body);
      // hiển thị confirm
      showConfirm(
        "Xác nhận thêm mới nhân viên?",
        "Bạn có chắc chắn muốn thêm nhân viên?",
        () => post(body)
      );
    } else {
      const body = {
        ...data, // giữ các biến cũ trong data
        trang_thai: chuyenDoiThanhEnum(data.trang_thai), // ghi đè thuộc tính trạng thái trong data, convert thành enum
        gioi_tinh: chuyenDoiThanhEnumGioiTinh(data.gioi_tinh),
        ngay_sinh: data?.ngay_sinh, // Chuyển đổi định dạng ngày tháng
        id: nhanVienHienTai?.id, // id của nhân viên cần cập nhật
      };
      console.log(body);
      // hiển thị confirm
      showConfirm(
        "Xác nhận cập nhật nhân viên?",
        "Bạn có chắc chắn muốn cập nhật nhân viên?",
        () => put(body, nhanVienHienTai?.id)
      );
    }
  };

  return (
    <>
      <FormProvider methods={methods} onSubmit={handleSubmit(onSubmit)}>
        <Row
          className="mt-10"
          gutter={25}
          style={{ display: "flex", justifyContent: "center" }}
        >
          <Col span={9}>
            <RHFInput
              label="Mã nhân viên"
              name="ma_nhan_vien"
              placeholder="Nhập tên nhân viên"
              required
            />
          </Col>
          <Col span={9}>
            <RHFInput
              label="Tên nhân viên"
              name="ten_nhan_vien"
              placeholder="Nhập tên nhân viên"
              required
            />
          </Col>

          <Col span={9}>
            <RHFInput
              label="Email"
              name="email"
              placeholder="Nhập mã email"
              required
            />
          </Col>

          <Col span={9}>
            <RHFInput
              label="Số điện thoại"
              name="so_dien_thoai"
              placeholder="Nhập mã số điện thoại nhân viên"
              required
            />
          </Col>

          <Col span={9}>
            <Controller
              name="mat_khau"
              control={control}
              render={({ field, fieldState: { error } }) => (
                <>
                  <label
                    className="mt-15 d-block"
                    style={{ fontWeight: "500", marginBottom: "13px" }}
                  >
                    Mật khẩu
                    <span className="required"></span>
                  </label>
                  <Input.Password
                    style={{ width: "100%" }}
                    placeholder="Nhập mật khẩu"
                    {...field}
                  />
                  {error && (
                    <span className="color-red mt-3 d-block">
                      {error?.message}
                    </span>
                  )}
                </>
              )}
            />
          </Col>

          <Col span={9}>
            <Controller
              name="gioi_tinh"
              control={control}
              render={({ field, fieldState: { error } }) => (
                <>
                  <label
                    className="mt-15 d-block"
                    style={{ fontWeight: "500" }}
                  >
                    Giới tính
                    <span className="required"></span>
                  </label>
                  <Select
                    style={{ width: "100%" }}
                    className="mt-13"
                    {...field}
                    placeholder="Chọn giới tính"
                  >
                    {DANH_SACH_GIOI_TINH_KHACH_HANG.map((gioiTinh, index) => {
                      return (
                        <>
                          <Option key={index} value={gioiTinh}>
                            {gioiTinh}
                          </Option>
                        </>
                      );
                    })}
                  </Select>
                  {error && (
                    <span className="color-red mt-3 d-block">
                      {error?.message}
                    </span>
                  )}
                </>
              )}
            />
          </Col>

          <Col span={9}>
            <Controller
              name="ngay_sinh"
              control={control}
              render={({ field, fieldState: { error } }) => {
                const value = field.value
                  ? dayjs(field.value, "DD/MM/YYYY")
                  : null;
                const isValidDate = value && value.isValid();
                return (
                  <>
                    <label
                      className="mt-15 d-block"
                      style={{ fontWeight: "500", marginBottom: "13px" }}
                    >
                      Ngày sinh
                      <span className="required"></span>
                    </label>
                    <DatePicker
                      style={{ width: "100%" }}
                      format="DD/MM/YYYY"
                      {...field}
                      placeholder="Chọn ngày sinh"
                      onChange={(date, dateString) =>
                        field.onChange(dateString)
                      }
                      value={isValidDate ? value : null}
                      disabledDate={(current) =>
                        current && current > dayjs().endOf("day")
                      }
                    />
                    {error && (
                      <span className="color-red mt-3 d-block">
                        {error?.message}
                      </span>
                    )}
                  </>
                );
              }}
            />
          </Col>

          <Col span={9}>
            <Controller
              name="trang_thai"
              control={control}
              render={({ field, fieldState: { error } }) => (
                <>
                  <label
                    className="mt-15 d-block"
                    style={{ fontWeight: "500" }}
                  >
                    Trạng thái
                    <span className="required"></span>
                  </label>
                  <Select
                    style={{ width: "100%" }}
                    className="mt-13"
                    {...field}
                    placeholder="Chọn trạng thái"
                  >
                    {DANH_SACH_TRANG_THAI_KHACH_HANG.map((trangThai, index) => {
                      return (
                        <>
                          <Option key={index} value={trangThai}>
                            {trangThai}
                          </Option>
                        </>
                      );
                    })}
                  </Select>
                  {error && (
                    <span className="color-red mt-3 d-block">
                      {error?.message}
                    </span>
                  )}
                </>
              )}
            />
          </Col>

          <Col
            span={18}
            style={{ display: "flex", justifyContent: "end" }}
            className="mt-10"
          >
            <Space className="mt-20 mb-5">
              <Button
                onClick={() => navigate(DUONG_DAN_TRANG.nhan_vien.danh_sach)}
              >
                Hủy bỏ
              </Button>
              <Button htmlType="submit" type="primary">
                {laCapNhat ? "Cập nhật" : "Lưu"}
              </Button>
            </Space>
          </Col>
        </Row>
      </FormProvider>
    </>
  );
}

const chuyenDoiThanhEnum = (trangThai) => {
  switch (trangThai) {
    case "Đang hoạt động":
      return "dang_hoat_dong";
    case "Ngừng hoạt động":
      return "ngung_hoat_dong";
    default:
      return null;
  }
};

const chuyenDoiEnumThanhTrangThai = (trangThai) => {
  switch (trangThai) {
    case "dang_hoat_dong":
      return "Đang hoạt động";
    case "ngung_hoat_dong":
      return "Ngừng hoạt động";
    default:
      return "Đang hoạt động";
  }
};

const chuyenDoiThanhEnumGioiTinh = (gioiTinh) => {
  switch (gioiTinh) {
    case "Nam":
      return 1;
    case "Nữ":
      return 0;
    default:
      return null;
  }
};

const chuyenDoiEnumThanhGioiTinh = (gioiTinh) => {
  switch (gioiTinh) {
    case 1:
      return "Nam";
    case 0:
      return "Nữ";
    default:
      return null; // Giá trị mặc định hoặc xử lý các giá trị không xác định nếu cần
  }
};
