import { Helmet } from 'react-helmet-async';

import { CONFIG } from 'src/config-global';

import { CropsView } from 'src/sections/crops/view'; 

export default function Page() {
  return (
    <>
      <Helmet>
        <title> {`Crops - ${CONFIG.appName}`} </title>
      </Helmet>

      <CropsView />
    </>
  );
}
