import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import Button from '@mui/material/Button';

import { SensorDataProps } from './types'; // Import the shared type

export function CropsTableRow({
  row,
  onOpenModal,
}: {
  row: SensorDataProps;
  onOpenModal: (data: SensorDataProps) => void;
}) {
  return (
    <TableRow>
      <TableCell>{row.id}</TableCell>
      <TableCell>{row.nitrogen}</TableCell>
      <TableCell>{row.phosphorous}</TableCell>
      <TableCell>{row.potassium}</TableCell>
      <TableCell>{row.temperature}</TableCell>
      <TableCell>{row.ph}</TableCell>
      <TableCell>{row.humidity}</TableCell>
      <TableCell>{row.rainfall}</TableCell>
      <TableCell>{row.timestamp}</TableCell>
      <TableCell>
        <Button onClick={() => onOpenModal(row)} variant="outlined" size="small">
          Recommend
        </Button>
      </TableCell>
    </TableRow>
  );
}
