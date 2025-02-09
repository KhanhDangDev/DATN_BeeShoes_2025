import PropTypes from 'prop-types';
import React from 'react';
import { Link, useLocation } from 'react-router-dom';
import { useEffect, useState } from 'react';
import useResponsive from '../../hooks/useResponsive';
import { Layout, Menu, Drawer } from 'antd';
import './navbar-vertical-style.css'
import { LogoMobile } from '../../components/Logo';
import { FaTags, FaUserGroup, FaUserTag, FaCartPlus, FaChartPie, FaSliders } from "react-icons/fa6";
import { RiShoppingBag3Fill } from "react-icons/ri";
import { DUONG_DAN_TRANG } from '../../routes/duong-dan';

NavbarVertical.propTypes = {
  isCollapse: PropTypes.bool,
  isOpenSidebar: PropTypes.bool,
  onCloseSidebar: PropTypes.func,
};

const { Sider } = Layout;

const menuStyle = {
  border: 'none'
};

const siderStyle = {
  height: '100vh',
  position: 'fixed',
  padding: '75px 7px 0px 7px',
  left: 0
};

const siderMobileStyle = {
  height: '100vh',
  position: 'fixed',
  padding: '5px 7px 0px 7px',
  left: 0
};

export default function NavbarVertical({ isCollapse, isOpenSidebar, onCloseSidebar }) {
  const { isMobile } = useResponsive();
  const { pathname } = useLocation();
  const [selectedKey, setSelectedKey] = useState('');
  const [openKeys, setOpenKeys] = useState([]);

  useEffect(() => {
    if (isOpenSidebar) {
      onCloseSidebar();
    }

    if (pathname.includes('/voucher')) {
      setSelectedKey(['voucher']);
      setOpenKeys([]);
    }
    else if (pathname.includes('/san-pham')) {
      setSelectedKey(['product']);
      setOpenKeys([]);
    }
    else if (pathname.includes('/danh-muc')) {
      setSelectedKey(['brand']);
      setOpenKeys([]);
    }
    else if (pathname.includes('/mau-sac')) {
      setSelectedKey(['color']);
      setOpenKeys(['attributes']);
    }
    else if (pathname.includes('/chat-lieu')) {
      setSelectedKey(['material']);
      setOpenKeys(['attributes']);
    }
    else if (pathname.includes('/thong-ke')) {
      setSelectedKey(['statistics']);
      setOpenKeys([]);
    }
    else if (pathname.includes('/don-hang')) {
      setSelectedKey(['bill']);
      setOpenKeys([]);
    }
    else if (pathname.includes('/khach-hang')) {
      setSelectedKey(['customer']);
      setOpenKeys(['account']);
    }
    else if (pathname.includes('/nhan-vien')) {
      setSelectedKey(['employee']);
      setOpenKeys(['account']);
    }
    else {
      setSelectedKey([]);
      setOpenKeys([]);
    }

    // #pathname
  }, [pathname]);

  const handleOpenChange = (keys) => {
    setOpenKeys(keys);
  };

  return (
    <>
      {!isMobile ?
        <Sider className={`nav-vertical`}
          trigger={null}
          collapsible
          theme='light'
          collapsed={isCollapse}
          style={siderStyle}
          width={255}
          collapsedWidth={86}
        >
          <Menu style={menuStyle}
            theme="light"
            mode="inline"
            selectedKeys={selectedKey}
            openKeys={openKeys}
            onOpenChange={handleOpenChange}
            items={items}
          />
        </Sider>
        :
        <Drawer
          width={315}
          placement={'left'}
          closable={false}
          onClose={onCloseSidebar}
          open={isOpenSidebar}
        >
          <LogoMobile />
          <Sider className='nav-vertical-mobile'
            trigger={null}
            collapsible
            theme='light'
            width={300}
            style={siderMobileStyle}
          >
            <Menu style={menuStyle}
              theme="light"
              mode="inline"
              selectedKeys={selectedKey}
              openKeys={openKeys}
              onOpenChange={handleOpenChange}
              items={items}
            />
          </Sider>
        </Drawer>
      }
    </>
  )
}

const ICONS = {
  attributes: <FaSliders color='#38B6FF' size={15} />,
  category: <FaTags color='#38B6FF' size={15} />,
  voucher: <FaUserTag color='#38B6FF' size={15} />,
  account: <FaUserGroup color='#38B6FF' size={15} />,
  statistics: <FaChartPie color='#38B6FF' size={15} />,
  bill: <FaCartPlus color='#38B6FF' size={15} />,
  product: <RiShoppingBag3Fill color='#38B6FF' size={15} />,
}

const labelStyle = {
  fontSize: 16,
  fontWeight: 500,
}

const SpanStyle = ({ label }) => (
  <span style={labelStyle}>{label}</span>
)

const items = [
  {
    key: 'statistics',
    label: <Link to={DUONG_DAN_TRANG.thong_ke}>
      <SpanStyle label="Thống kê" />
    </Link>,
    icon: ICONS.statistics,
  },
  {
    key: 'bill',
    label: <Link to={DUONG_DAN_TRANG.don_hang.danh_sach}>
      <SpanStyle label="Quản lý đơn hàng" />
    </Link>,
    icon: ICONS.bill,
  },
  {
    key: 'product',
    label: <Link to={DUONG_DAN_TRANG.san_pham.danh_sach}>
      <SpanStyle label="Quản lý sản phẩm" />
    </Link>,
    icon: ICONS.product,
  },
  {
    key: 'attributes',
    label: <SpanStyle label='Quản lý thuộc tính' />,
    icon: ICONS.attributes,
    children: [
      {
        key: 'color',
        label:
          <Link to={DUONG_DAN_TRANG.san_pham.mau_sac}>
            <SpanStyle label="Màu sắc" />
          </Link>
      },
      {
        key: 'material',
        label:
          <Link to={DUONG_DAN_TRANG.san_pham.chat_lieu}>
            <SpanStyle label="Chất liệu" />
          </Link>
      },
    ],
  },
  {
    key: 'brand',
    label: <Link to={DUONG_DAN_TRANG.san_pham.thuong_hieu}>
      <SpanStyle label="Quản lý danh mục" />
    </Link>,
    icon: ICONS.category,
  },
  {
    key: 'account',
    label: <SpanStyle label='Tài khoản' />,
    icon: ICONS.account,
    children: [
      {
        key: 'customer',
        label:
          <Link to={DUONG_DAN_TRANG.khach_hang.danh_sach}>
            <SpanStyle label='Khách hàng' />
          </Link>
      },
      {
        key: 'employee',
        label:
          <Link to={DUONG_DAN_TRANG.nhan_vien.danh_sach}>
            <SpanStyle label='Nhân viên' />
          </Link>
      },
    ],
  },
  {
    key: 'voucher',
    label: <Link to={DUONG_DAN_TRANG.voucher.danh_sach}>
      <SpanStyle label="Mã giảm giá" />
    </Link>,
    icon: ICONS.voucher,
  },
];
