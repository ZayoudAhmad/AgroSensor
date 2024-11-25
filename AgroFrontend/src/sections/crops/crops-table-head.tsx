import TableRow from '@mui/material/TableRow';
import TableCell from '@mui/material/TableCell';
import TableHead from '@mui/material/TableHead';

type CropsTableHeadProps = {
  headers: { id: string; label: string }[];
};

export function CropsTableHead({ headers }: CropsTableHeadProps) {
  return (
    <TableHead>
      <TableRow>
        {headers.map((header) => (
          <TableCell key={header.id}>{header.label}</TableCell>
        ))}
      </TableRow>
    </TableHead>
  );
}
