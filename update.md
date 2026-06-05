# CuCustomField baserCMS5 完全5系化 仕様書

## 1. 目的
- 4系最新版 CuCustomField の全機能を baserCMS5 系で再現する。
- 4系 baserCMS サイトを 5系化した際に、移行前と同一の業務動作を完遂できる状態を実現する。
- 既存運用データを破壊せず、4系からの移行を安全に行う。
- 将来保守のため、5系標準の Service 指向へ整理する。

## 2. 絶対要件
- DB 構造は 4系のテーブル/カラム/型/意味を正とし、完全互換を維持する。
- Helper は 4系の公開メソッドについて、引数と戻り値を極力同一にする。
- 現在 5系化作業で未使用の Service を導入し、Controller から Model 直接依存を段階的に排除する。
- 4系最新版で提供している機能は、管理画面・保存・表示・検索・連携を含めて再現する。
- 管理画面のループフィールド UI は、現行 jQuery バージョン上で正常動作するよう互換修正する。
- fieldValue による絞り込み機能を 5系でも維持する。

## 3. 対象範囲
- プラグイン本体
- フィールドタイプ子プラグイン
  - CuCfCheckbox
  - CuCfDate
  - CuCfDatetime
  - CuCfFile
  - CuCfGooglemaps
  - CuCfMultiple
  - CuCfPref
  - CuCfRadio
  - CuCfRelated
  - CuCfSelect
  - CuCfText
  - CuCfTextarea
  - CuCfWysiwyg
- 管理画面（設定・定義・値）
- フロント表示 Helper
- カスタム検索（GET パラメータ）
- CuApprover 連携に必要なイベント連携

## 4. 非対象（本仕様では行わない）
- 新規テーブル追加
- 4系に存在しない独自機能の追加
- API 互換を破壊する命名変更

## 5. 現状差分（5系化途中で確認済み）
- Migration 初期定義に 4系との差異がある。
  - `cu_custom_field_definitions.field_type` が `type` になっている。
  - `counter` カラムが不足している。
  - `status` 型が 4系 boolean と不一致（5系では integer 定義）。
- Controller が Table/Model 直接呼び出し中心で、Service 層が不足している。
- Helper は一部 5系対応済みだが、4系互換ポリシー（引数/戻り値保証）が文書化されていない。

## 6. DB 互換仕様（4系準拠）

### 6.1 テーブル
- `cu_custom_field_configs`
- `cu_custom_field_definitions`
- `cu_custom_field_values`

### 6.2 カラム互換ルール
- 4系 Schema のカラム名を変更しない。
- 4系 Schema の意味が変わる型変更を行わない。
- 既存データ移行で値変換が必要な場合は、移行 SQL/マイグレーションで吸収する。
- 4系 DB ダンプを投入した際、DDL 追加変更なしでプラグイン機能が稼働することを必須とする。

### 6.3 必須修正項目
- Migration の `type` を `field_type` に修正。
- `counter` カラムを追加。
- `status` の型/デフォルトを 4系互換へ調整。
- 4系既存 DB をそのまま読み込めることを保証。

## 7. Service 導入仕様

### 7.1 新設 Service
- `CuCustomFieldConfigsService`
- `CuCustomFieldDefinitionsService`
- `CuCustomFieldValuesService`
- `CuCustomFieldDefinitionTreeService`（並び替え・親子制御）
- `CuCustomFieldSearchService`（カスタム検索条件組み立て）
- `CuCustomFieldValueTransformService`（保存値の整形/シリアライズ互換）

### 7.2 Interface
- すべて Interface を定義し、Controller/Helper から Interface を参照する。
- Service Locator 経由で取得し、将来の差し替えを可能にする。

### 7.3 Controller 方針
- Controller は入力受付・レスポンス制御に限定する。
- 既存の `find()/save()/delete()/up()/down()` 直呼びを Service 呼び出しへ置換する。
- 例外は Service 層でドメイン例外へ正規化し、Controller 側でメッセージ変換する。

## 8. Helper 互換仕様（4系 API 準拠）

### 8.1 互換対象（代表）
- `setup($contentId)`
- `getFieldAttribute($post, $field, $attribute = 'label_name')`
- `getFieldConfigList($contentId)`
- `getFieldConfig($contentId, $fieldName)`
- `getFieldConfigChoice($contentId, $fieldName)`
- `get($post = [], $field = '', $options = [])`
- `input($field, $definition, $options = [])`
- `judgeShowFieldConfig($data = [], $options = [])`
- `judgeStatus($data = [])`
- `hasCustomField($data = [])`
- `allowPublish($data, $modelName = '')`

### 8.2 互換ルール
- 引数の省略時デフォルト値は 4系互換を維持する。
- 戻り値型（string/array/bool/int）と空値時の挙動を 4系互換に揃える。
- `loop` 型や `multiple` 型の値変換（serialize/unserialize 相当）は互換を維持する。
- 5系独自の Entity 入力に対応しつつ、4系配列入力でも同一結果になるよう吸収する。

## 9. 機能再現仕様（4系完全再現）

### 9.1 管理機能
- カスタムフィールド設定 CRUD
- フィールド定義 CRUD
- フィールド定義並び替え（up/down）
- 公開/非公開切替（ajax）
- 重複チェック（ajax）
- ループ項目の親子構造管理
- ループフィールド追加・削除・並び替えの管理画面操作が現行 jQuery で成立すること

### 9.2 入力・保存
- Blog 投稿画面への入力欄差し込み
- フィールドタイプ別入力 UI
- 必須/バリデーション/正規表現チェック
- デフォルト値
- ファイルアップロード（拡張子制限）

### 9.3 表示
- テンプレートからの値取得
- フィールドタイプ別表示変換
- ループデータの表示
- 関連データ取得

### 9.4 検索
- GET パラメータによるカスタム検索
- 複数パラメータ同時絞り込み
- fieldValue を利用した絞り込み条件の後方互換を維持する

### 9.5 外部連携
- CuApprover 下書き連携のイベント維持

## 10. フィールドタイプ再現要件
- 4系で提供している全タイプを 5系で再現する。
- 各タイプで以下を満たす。
  - 管理画面入力
  - 保存
  - フロント表示
  - 検索連携（対象タイプ）
  - バリデーション
- 既存の子プラグイン構成を尊重し、タイプ追加拡張（CREATE_FIELD_TYPE.md の考え方）を維持する。

## 11. 移行仕様

### 11.1 DB 移行
- 既存 4系 DB からの移行時にテーブル名/カラム名は変更しない。
- 差分カラムがある場合は追加マイグレーションで補正する。
- データ変換が必要な場合は idempotent な移行処理で実施する。

### 11.2 データ互換
- 既存 `cu_custom_field_values` の `key/value/model` はそのまま解釈できること。
- 既存 `option_meta`/`choices`/`validate` のシリアライズデータを読み書き可能とする。

## 12. テスト仕様

### 12.1 単体テスト
- Service 単位で正常系/異常系をカバー。
- 値変換（serialize/unserialize）互換テストを追加。

### 12.2 結合テスト
- 管理画面 CRUD 一連フロー。
- Blog 投稿画面で入力 -> 保存 -> 再編集 -> 表示。
- 検索 GET パラメータの動作確認。

### 12.3 互換テスト
- 4系 fixture を用いた比較テスト。
- Helper の戻り値比較（4系期待値ベース）。
- 主要フィールドタイプの golden テスト。

## 13. 受け入れ基準
- 4系全フィールドタイプで「設定・入力・保存・表示・検索」が成立する。
- 4系 DB を投入してエラーなく稼働する。
- 4系 DB を投入した状態で、テーブル定義変更なしに管理画面とフロントの主要導線が動作する。
- 4系 Helper 呼び出しコードを大きく変更せずにテンプレートが動作する。
- Controller の主要ユースケースで Service 経由になっている。
- 既知の 5系化途中差分（`field_type`/`counter`/`status`）が解消されている。
- 管理画面のループフィールド操作が現行 jQuery 環境で崩れない。
- fieldValue 絞り込みが 4系同等の条件解釈で動作する。

## 14. 実装優先順位
1. DB 互換修正（Migration/Schema 補正）
2. Service 層新設と Controller 置換
3. Helper 互換保証（引数/戻り値）
4. 全フィールドタイプの動作再確認
5. 連携・検索・回帰テスト

## 15. リスクと対策
- リスク: シリアライズ互換崩れで既存値が表示不能。
  - 対策: 4系データ fixture で読み書き往復テスト。
- リスク: 5系 Entity 化で配列前提コードが崩れる。
  - 対策: Helper で配列/Entity 両対応の吸収層を維持。
- リスク: Service 化でメッセージ/挙動が変化。
  - 対策: 既存 UI 文言・遷移を結合テストで固定。

## 16. 成果物
- 本仕様書（update.md）
- Service Interface/実装
- DB 差分補正 Migration
- 互換テスト一式
