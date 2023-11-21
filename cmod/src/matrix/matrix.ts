export default class Matrix {
    protected data: Array<Array<number>>;

    public constructor(r: number, c: number) {
        this.data = [];
        for(let i = 0; i < r; i++) {
            this.data.push([]);
            for(let j = 0; j < c; j++) {
                this.data[i].push(0);
            }
        }
    }

    public get(row: number, col: number): number {
        return this.data[row][col];
    }

    public set(row: number, col: number, value: number): void {
        this.data[row][col] = value;
    }

    public get rows(): number {
        return this.data.length;
    }

    public get cols(): number {
        return this.rows > 0 ? this.data[0].length : 0;
    }

    public minor(row: number, col: number): Matrix {
        if(this.rows <= 1 || this.cols <= 1) {
            throw new Error("Could not get minor matrix of 1x1 matrix");
        }
        let m = new Matrix(this.rows - 1, this.cols - 1);
        for(let r = 0; r < this.rows; r++) {
            for (let c = 0; c < this.cols; c++) {
                if(r == row || c == col) {
                    continue;
                }
                let roffset = 0;
                let coffset = 0;
                if(r > row) roffset = 1;
                if(c > col) coffset = 1;
                m.set(r - roffset, c - coffset, this.get(r, c));
            }
        }
        return m;
    }

    public cofactor(row: number, col: number): number {
        if (this.rows != this.cols) {
            throw new Error("Could not compute co-factor for non square matrix");
        }
        if (this.rows == 1) {
            return Math.pow(-1, row + col) * this.get(0, 0);
        }
        return Math.pow(-1, row + col) * this.minor(row, col).determinant();
    }

    public determinant(): number {
        if(this.rows != this.cols) {
            throw new Error("Could not compute determinant for non square matrix");
        }
        if(this.rows == 1) {
            return this.get(0, 0);
        }
        let s = 0;
        for(let i = 0; i < this.rows; i++) {
            s += this.get(0,i) * this.cofactor(0, i);
        }
        return s;
    }

    public inverse(): Matrix {
        if(this.rows != this.cols) {
            throw new Error("Could not compute inverse matrix for non-square matrix");
        }
        let i = new Matrix(this.rows, this.rows);
        let d = this.determinant();
        for(let r = 0; r < this.rows; r++) {
            for(let c = 0; c < this.cols; c++) {
                i.set(r, c, this.minor(r, c).determinant() / d);
            }
        }
        return i.transpose();
    }

    public transpose(): Matrix {
        let m = new Matrix(this.cols, this.rows);
        for(let r = 0; r < this.rows; r++) {
            for(let c = 0; c < this.cols; c++) {
                m.set(c, r, this.get(r, c));
            }
        }
        return m;
    }

    public static scale(m: Matrix, f: number): Matrix {
        let v = new Matrix(m.rows, m.cols);
        for(let r = 0; r < m.rows; r++) {
            for(let c = 0; c < m.cols; c++) {
                v.set(r, c, m.get(r, c) * f);
            }
        }
        return v;
    }

    public static add(lhs: Matrix, rhs: Matrix): Matrix {
        if(!(lhs.cols == rhs.cols && lhs.rows == rhs.rows)) {
            throw new Error("Could not add matrixes of unequal sizes");
        }
        let result = new Matrix(lhs.rows, rhs.rows);
        for(let r = 0; r < lhs.rows; r++) {
            for(let c = 0; c < lhs.cols; c++) {
                result.set(r, c, lhs.get(r,c) + rhs.get(r,c));
            }
        }
        return result;
    }

    public static mult(lhs: Matrix, rhs: Matrix): Matrix {
        if(!(lhs.cols == rhs.rows)) {
            throw new Error("Could not multiply matrixes: invalid dimensions");
        }
        let result = new Matrix(lhs.rows, rhs.cols);
        for(let r = 0; r < lhs.rows; r++) {
            for(let c = 0; c < rhs.cols; c++) {
                let k = 0;
                for(let cc = 0; cc < lhs.rows; cc++) {
                    k += lhs.get(r, cc) * rhs.get(cc, c);
                }
                result.set(r,c,k);
            }
        }
        return result;
    }

    public static identity(s: number): Matrix {
        let res = new Matrix(s, s);
        for(let i = 0; i < s; i++) {
            res.set(i, i, 1);
        }
        return res;
    }
}
