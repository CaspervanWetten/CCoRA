export default interface DrawerOptions {
    minZoom: number;
    maxZoom: number;
    width:   number;
    height:  number;
    gridOptions: GridOptions;
}

interface GridOptions {
    drawGrid: boolean;
    snapGrid: boolean;
    horizontalGridSeperation: number;
    verticalGridSeperation: number;
}

export { GridOptions };
