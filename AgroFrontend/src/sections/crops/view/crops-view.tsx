import React, { useState, useEffect } from 'react';
import Card from '@mui/material/Card';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableContainer from '@mui/material/TableContainer';
import TablePagination from '@mui/material/TablePagination';
import Typography from '@mui/material/Typography';
import Modal from '@mui/material/Modal';
import Box from '@mui/material/Box';
import Button from '@mui/material/Button';
import axios from 'axios';

import { Scrollbar } from 'src/components/scrollbar';
import { CropsTableHead } from '../crops-table-head';
import { CropsTableRow } from '../crops-table-row';
import { CropsTableToolbar } from '../crops-table-toolbar';

import { SensorDataProps } from '../types';

const modalStyle = {
  position: 'absolute',
  top: '50%',
  left: '50%',
  transform: 'translate(-50%, -50%)',
  width: 400,
  bgcolor: 'background.paper',
  boxShadow: 24,
  p: 4,
  borderRadius: 2,
};

type RecommendationResponse = {
  top_crops: Array<{
    confidence: number;
    crop: string;
  }>;
};

export function CropsView() {
  const [cropsData, setCropsData] = useState<SensorDataProps[]>([]);
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(5);
  const [filterName, setFilterName] = useState('');
  const [modalOpen, setModalOpen] = useState(false);
  const [selectedData, setSelectedData] = useState<SensorDataProps | null>(null);
  const [recommendation, setRecommendation] = useState<RecommendationResponse | null>(null);

  useEffect(() => {
    axios
      .get('http://localhost:8000/api/sensordata')
      .then((response) => setCropsData(response.data))
      .catch((error) => console.error(error));
  }, []);

  const handleFilterByName = (event: React.ChangeEvent<HTMLInputElement>) => {
    setFilterName(event.target.value);
  };

  const filteredData = cropsData.filter((row) =>
    row.id && row.id.toString().includes(filterName)
  );

  const handleOpenModal = async (data: SensorDataProps) => {
    setSelectedData(data);
    setModalOpen(true);

    // Call the recommendation API
    try {
      const response = await axios.post('http://localhost:8000/api/recommendation', data);
      setRecommendation(response.data);
    } catch (error) {
      console.error('Failed to fetch recommendation:', error);
      setRecommendation(null);
    }
  };

  const handleCloseModal = () => {
    setModalOpen(false);
    setSelectedData(null);
    setRecommendation(null);
  };

  return (
    <>
      <Card>
        <CropsTableToolbar filterName={filterName} onFilterName={handleFilterByName} />

        <Scrollbar>
          <TableContainer>
            <Table>
              <CropsTableHead
                headers={[
                  { id: 'id', label: 'ID' },
                  { id: 'nitrogen', label: 'Nitrogen' },
                  { id: 'phosphorous', label: 'Phosphorous' },
                  { id: 'potassium', label: 'Potassium' },
                  { id: 'temperature', label: 'Temperature (°C)' },
                  { id: 'ph', label: 'pH' },
                  { id: 'humidity', label: 'Humidity (%)' },
                  { id: 'rainfall', label: 'Rainfall (mm)' },
                  { id: 'timestamp', label: 'Timestamp' },
                  { id: 'action', label: 'Action' },
                ]}
              />

              <TableBody>
                {filteredData
                  .slice(page * rowsPerPage, page * rowsPerPage + rowsPerPage)
                  .map((row) => (
                    <CropsTableRow key={row.id} row={row} onOpenModal={handleOpenModal} />
                  ))}
              </TableBody>
            </Table>
          </TableContainer>
        </Scrollbar>

        <TablePagination
          page={page}
          rowsPerPage={rowsPerPage}
          count={filteredData.length}
          onPageChange={(e, newPage) => setPage(newPage)}
          onRowsPerPageChange={(e) => setRowsPerPage(parseInt(e.target.value, 10))}
        />
      </Card>

      <Modal open={modalOpen} onClose={handleCloseModal}>
        <Box sx={modalStyle}>
          <Typography variant="h6" component="h2">
            Recommended Crops
          </Typography>

          {recommendation ? (
            <Box mt={2}>
              {recommendation.top_crops.map((crop, index) => (
                <Box key={index} display="flex" justifyContent="space-between" mt={1}>
                  <Typography variant="body1">{crop.crop}</Typography>
                  <Typography variant="body2">
                    Confidence: {(crop.confidence * 100).toFixed(2)}%
                  </Typography>
                </Box>
              ))}
            </Box>
          ) : (
            <Typography mt={2}>Loading recommendation...</Typography>
          )}

          <Button onClick={handleCloseModal} sx={{ mt: 2 }} variant="contained">
            Close
          </Button>
        </Box>
      </Modal>
    </>
  );
}
