import { useState } from 'react';

import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import Button from '@mui/material/Button';
import Checkbox from '@mui/material/Checkbox';

import axios from 'axios';

export type UserProps = {
  id: string;
  type: string;
  latitude: number;
  longitude: number;
  status: string;
};

type UserTableRowProps = {
  row: UserProps;
  selected: boolean;
  onSelectRow: () => void;
};

export function UserTableRow({ row, selected, onSelectRow }: UserTableRowProps) {
  const [status, setStatus] = useState(row.status);

  const toggleStatus = async () => {
    try {
      const newStatus = status === 'Active' ? 'Inactive' : 'Active';
      await axios.patch(`http://localhost:8000/api/sensor/${row.id}/toggle-status`, {
        status: newStatus,
      });
      setStatus(newStatus);
    } catch (error) {
      console.error('Error toggling status:', error);
    }
  };

  return (
    <TableRow hover selected={selected}>
      <TableCell padding="checkbox">
        <Checkbox checked={selected} onChange={onSelectRow} />
      </TableCell>
      <TableCell align="left">{row.id}</TableCell>
      <TableCell align="left">{row.type}</TableCell>
      <TableCell align="left">{row.latitude}</TableCell>
      <TableCell align="left">{row.longitude}</TableCell>
      <TableCell align="center">
        <Button
          variant="outlined"
          color={status === 'Active' ? 'success' : 'error'}
          onClick={toggleStatus}
        >
          {status}
        </Button>
      </TableCell>
    </TableRow>
  );
}
