import { useState, useEffect } from 'react';
import * as Yup from 'yup';
import axios from 'axios';
import { useNavigate } from "react-router-dom"
import { formatCurrencyVnd, formatNumber } from '../../../utils/formatCurrency';
// form
import { yupResolver } from '@hookform/resolvers/yup';
import { useForm, Controller } from 'react-hook-form';
// antd
import { Col, Tag, Select, Row, Button, Space, Input, DatePicker } from "antd"
import moment from 'moment';
// routes
import { DUONG_DAN_TRANG } from "../../../routes/duong-dan"
// components
import FormProvider from '../../../components/hook-form/FormProvider';
import RHFInput from '../../../components/hook-form/RHFInput';
// hooks
import useConfirm from '../../../hooks/useConfirm';
import useNotification from '../../../hooks/useNotification';
import useLoading from '../../../hooks/useLoading';

const { Option } = Select;

// ----------------------------------------------------------------------

export default function FormThemSuaDanhMuc({ laCapNhat, thuongHieuHienTai }) {
  const { onOpenSuccessNotify } = useNotification(); //mở thông báo
  const { showConfirm } = useConfirm(); // mở confirm
  const { onOpenLoading, onCloseLoading } = useLoading(); //mở, tắt loading

  const navigate = useNavigate();

  // validate
  const ThuongHieuSchema = Yup.object().shape({
    ma: Yup.string().trim().required('Tên danh mục không được bỏ trống'),
    ten: Yup.string().trim().required('Mã danh mục không được bỏ trống'),
    //     donGia: Yup.string().required('Đơn giá không được bỏ trống'),
    //     idThuongHieu: Yup.string().required('Thương hiệu không được bỏ trống'),
  });

  // giá trị mặc định của biến, tương tự useState
  const defaultValues = {
    ma: thuongHieuHienTai?.ma_thuong_hieu || '',
    ten: thuongHieuHienTai?.ten_thuong_hieu || '',
  };

  // lấy methods từ use form
  const methods = useForm({
    resolver: yupResolver(ThuongHieuSchema),
    defaultValues,
  });

  // các phương thức của methods
  const {
    reset,
    control,
    handleSubmit,
  } = methods;

  useEffect(() => {
    // nếu là trang cập nhật => sẽ reset lại các biến trong defaultValues
    if (laCapNhat && thuongHieuHienTai) {
      reset(defaultValues);
    }
    // nếu là trang thêm mới => sẽ reset lại các biến trong defaultValues
    if (!laCapNhat) {
      reset(defaultValues);
    }
  }, [laCapNhat, thuongHieuHienTai]) // gọi useEffect này mỗi khi các tham số truyền vào thay đỏi

  // hàm gọi api thêm mới khách hàng
  const post = async (body) => {
    try {
      const response = await axios.post("http://127.0.0.1:8000/api/add-thuong-hieu", body); // gọi api
      navigate(DUONG_DAN_TRANG.san_pham.thuong_hieu);
      // navigate(DUONG_DAN_TRANG.san_pham.cap_nhat_thuong_hieu(response.data.data.id)); // chuyển sang trang cập nhật
      onOpenSuccessNotify('Thêm mới danh mục thành công!') // hiển thị thông báo 
    } catch (error) {
      console.log(error);
    }
  }

  const put = async (body, id) => {
    try {
      const response = await axios.put(`http://127.0.0.1:8000/api/update-thuong-hieu/${id}`, body); // gọi API cập nhật
      navigate(DUONG_DAN_TRANG.san_pham.cap_nhat_thuong_hieu(response.data.data.id)); // chuyển sang trang cập nhật
      onOpenSuccessNotify('Cập nhật danh mục thành công!'); // hiển thị thông báo 
    } catch (error) {
      console.log(error);
    }
  }

  const onSubmit = async (data) => {
    if (!laCapNhat) {
      const body = {
        ...data, // giữ các biến cũ trong data 
      }
      console.log(body);
      // hiển thị confirm
      showConfirm("Xác nhận thêm mới danh mục?", "Bạn có chắc chắn muốn thêm danh mục?", () => post(body));
    } else {
      const body = {
        ...data, // giữ các biến cũ trong data 
      }
      console.log(body);
      // hiển thị confirm
      showConfirm("Xác nhận cập nhật danh mục?", "Bạn có chắc chắn muốn cập nhật danh mục?", () => put(body, thuongHieuHienTai?.id));
    }
  }

  return (
    <>
      <FormProvider methods={methods} onSubmit={handleSubmit(onSubmit)}>
        <Row className='mt-10' gutter={25} style={{ display: "flex", justifyContent: "center" }}>

          <Col span={9}>
            <RHFInput
              label='Mã danh mục'
              name='ma'
              placeholder='Nhập mã danh mục'
              required
            />
          </Col>

          <Col span={9}>
            <RHFInput
              label='Tên danh mục'
              name='ten'
              placeholder='Nhập tên danh mục'
              required
            />
          </Col>

          {/*
          <Col span={9} style={{ marginTop: 15 }}>
            <Controller
              name='trangThai'
              control={control}
              render={({ field, fieldState: { error } }) => (
                <>
                  <Select
                    {...field}
                    style={{ width: WIDTH_SELECT }}
                    placeholder="Trạng thái"
                  >
                    {DANH_SACH_TRANG_THAI_DANH_MUC.map((trangThai, index) => {
                      return (
                        <>
                          <Option key={index} value={trangThai}>{trangThai}</Option>
                        </>
                      )
                    })}
                  </Select>
                  {error && <span className='color-red mt-3 d-block'>{error?.message}</span>}
                </>
              )}
            />
          </Col>
          */}

          <Col span={18} style={{ display: 'flex', justifyContent: 'end' }} className="mt-10">
            <Space className='mt-20 mb-5'>
              <Button onClick={() => navigate(DUONG_DAN_TRANG.san_pham.thuong_hieu)}>Hủy bỏ</Button>
              <Button
                htmlType='submit'
                type='primary'
              >
                {laCapNhat ? 'Cập nhật' : 'Lưu'}
              </Button>
            </Space>
          </Col>

        </Row>

      </FormProvider>
    </>
  )
}

const WIDTH_SELECT = 300;
const DANH_SACH_TRANG_THAI_DANH_MUC = ['Đang hoạt động', 'Ngừng hoạt động'];
